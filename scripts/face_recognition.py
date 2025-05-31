import cv2
import os
import numpy as np
from sklearn.svm import SVC
from deepface import DeepFace
from joblib import dump, load
import sys

# Ambil argumen dari Laravel
if len(sys.argv) != 3:
    print("false")
    sys.exit(1)
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
DATASET_PATH = sys.argv[1]  # folder wajah user (contoh: storage/app/public/foto/username)
TEST_IMAGE_PATH = sys.argv[2]  # path ke foto base64 yang sudah disimpan sementara
# FAKE_PATH = os.path.join(os.getcwd(), 'storage', 'app', 'public', 'palsu')
FAKE_PATH = os.path.abspath(os.path.join(BASE_DIR, '..', 'storage', 'app', 'public', 'palsu'))

MODEL_PATH = os.path.join(DATASET_PATH, 'svm_model.joblib')

def preprocess(image):
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    blur = cv2.GaussianBlur(gray, (3, 3), 0)
    equalized = cv2.equalizeHist(blur)
    return cv2.cvtColor(equalized, cv2.COLOR_GRAY2BGR)

def extract_face(image):
    face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    faces = face_cascade.detectMultiScale(gray, 1.3, 5)
    for (x, y, w, h) in faces:
        return image[y:y+h, x:x+w]
    return image  # fallback kalau tidak terdeteksi

def calculate_lbp(image, radius=2, neighbors=16):
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    lbp = np.zeros_like(gray)
    for i in range(radius, gray.shape[0] - radius):
        for j in range(radius, gray.shape[1] - radius):
            center = gray[i, j]
            binary_string = ''
            for n in range(neighbors):
                y = i + int(radius * np.sin(2 * np.pi * n / neighbors))
                x = j + int(radius * np.cos(2 * np.pi * n / neighbors))
                binary_string += '1' if gray[y, x] >= center else '0'
            lbp[i, j] = int(binary_string, 2) % 256
    hist, _ = np.histogram(lbp.ravel(), bins=256, range=(0, 256))
    return hist / (np.sum(hist) + 1e-6)

def load_dataset(dataset_path):
    dataset_features = []
    labels = []

    # Load wajah asli
    for filename in os.listdir(dataset_path):
        img_path = os.path.join(dataset_path, filename)
        image = cv2.imread(img_path)
        if image is not None:
            image = extract_face(preprocess(image))
            image = cv2.resize(image, (100, 100))
            features = calculate_lbp(image)
            dataset_features.append(features)
            labels.append('real')  # label untuk wajah asli

    # Load wajah palsu
    for filename in os.listdir(FAKE_PATH):
        img_path = os.path.join(FAKE_PATH, filename)
        image = cv2.imread(img_path)
        if image is not None:
            image = extract_face(preprocess(image))
            image = cv2.resize(image, (100, 100))
            features = calculate_lbp(image)
            dataset_features.append(features)
            labels.append('fake')  # label untuk wajah palsu

    return np.array(dataset_features), np.array(labels)

def train_model(dataset_path):
    X, y = load_dataset(dataset_path)
    model = SVC(kernel='rbf', probability=True, C=10.0, gamma='scale')
    model.fit(X, y)
    dump(model, MODEL_PATH)
    return model

def verify_face(dataset_path, test_image_path):
    # Load gambar input
    image = cv2.imread(test_image_path)
    if image is None:
        print("false")
        return

    image = extract_face(preprocess(image))
    image = cv2.resize(image, (100, 100))
    lbp_feature = calculate_lbp(image)
    print("Checking model existence:", os.path.exists(MODEL_PATH))
    if os.path.exists(MODEL_PATH):
        model = load(MODEL_PATH)
        print("Model loaded from:", MODEL_PATH)
    else:
        print("Training model...")
        model = train_model(dataset_path)
        print("Model trained and saved to:", MODEL_PATH)

    try:
        prediction = model.predict([lbp_feature])[0]
        confidence = model.predict_proba([lbp_feature]).max()
        print("Prediction:", prediction)
        print("Confidence:", confidence)
    except:
        print("Prediction error:", str(e))
        print("false")
        return

    try:
        result = DeepFace.find(img_path=test_image_path, db_path=dataset_path, model_name="ArcFace", detector_backend="mtcnn", enforce_detection=False)
        print("Face match result:", result[0])
        face_match = len(result[0]) > 0
        print(face_match)
    except Exception as e:
        print("DeepFace error:", str(e))
        face_match = False
        print(face_match)

    if confidence < 0.8 and face_match:
        print("true")
    else:
        print("false")

# Eksekusi
verify_face(DATASET_PATH, TEST_IMAGE_PATH)
# print("Current Working Directory:", os.getcwd())


# import cv2
# import os
# import numpy as np
# from sklearn.svm import SVC
# from deepface import DeepFace
# from joblib import dump, load
# import sys

# # ---------- CONFIG ----------
# DATASET_PATH = sys.argv[1]
# MODEL_PATH = 'svm_model.joblib'
# CAM_INDEX = 0
# # ----------------------------

# def preprocess(image):
#     gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
#     blur = cv2.GaussianBlur(gray, (3, 3), 0)
#     equalized = cv2.equalizeHist(blur)
#     return cv2.cvtColor(equalized, cv2.COLOR_GRAY2BGR)

# def extract_face(image):
#     face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')
#     gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
#     faces = face_cascade.detectMultiScale(gray, 1.3, 5)
#     for (x, y, w, h) in faces:
#         return image[y:y+h, x:x+w]
#     return image

# def calculate_lbp(image, radius=2, neighbors=16):
#     gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
#     lbp = np.zeros_like(gray)
#     for i in range(radius, gray.shape[0] - radius):
#         for j in range(radius, gray.shape[1] - radius):
#             center = gray[i, j]
#             binary_string = ''
#             for n in range(neighbors):
#                 y = i + int(radius * np.sin(2 * np.pi * n / neighbors))
#                 x = j + int(radius * np.cos(2 * np.pi * n / neighbors))
#                 binary_string += '1' if gray[y, x] >= center else '0'
#             lbp[i, j] = int(binary_string, 2) % 256
#     hist, _ = np.histogram(lbp.ravel(), bins=256, range=(0, 256))
#     return hist / (np.sum(hist) + 1e-6)

# def load_dataset(user_folder):
#     dataset_features = []
#     labels = []

#     folder_path = os.path.join(DATASET_PATH, user_folder)
#     for filename in os.listdir(folder_path):
#         img_path = os.path.join(folder_path, filename)
#         image = cv2.imread(img_path)
#         if image is not None:
#             image = extract_face(preprocess(image))
#             image = cv2.resize(image, (100, 100))
#             features = calculate_lbp(image)
#             dataset_features.append(features)
#             labels.append(user_folder)
#     return np.array(dataset_features), np.array(labels)

# def train_model(user_folder):
#     X, y = load_dataset(user_folder)
#     model = SVC(kernel='rbf', probability=True, C=10.0, gamma='scale')
#     model.fit(X, y)
#     dump(model, MODEL_PATH)
#     print("[INFO] Model trained and saved.")
#     return model

# def detect_face_and_check(user_folder):
#     cap = cv2.VideoCapture(CAM_INDEX)
#     if not cap.isOpened():
#         print("false")
#         return False

#     ret, frame = cap.read()
#     cap.release()

#     if not ret:
#         print("false")
#         return False

#     image = extract_face(preprocess(frame))
#     image = cv2.resize(image, (100, 100))
#     cv2.imwrite('captured.jpg', image)

#     lbp_feature = calculate_lbp(image)
#     model = load(MODEL_PATH) if os.path.exists(MODEL_PATH) else train_model(user_folder)
#     prediction = model.predict([lbp_feature])[0]
#     confidence = model.predict_proba([lbp_feature]).max()
#     print(f"[INFO] Prediksi texture: {prediction}, confidence: {confidence:.2f}")

#     try:
#         result = DeepFace.find(img_path="captured.jpg", db_path=os.path.join(DATASET_PATH, user_folder), model_name="ArcFace", detector_backend="mtcnn", enforce_detection=False)
#         face_match = len(result[0]) > 0
#         print("[INFO] Face match: ", "✓" if face_match else "✗")
#     except Exception as e:
#         print(f"[ERROR] DeepFace error: {e}")
#         face_match = False

#     if confidence > 0.65 and face_match:
#         print("true")
#         return True
#     else:
#         print("false")
#         return False

# # Jalankan program
# if __name__ == "__main__":
#     if not os.path.exists(MODEL_PATH):
#         train_model()
#     detect_face_and_check()