import cv2
import numpy as np
import os
import matplotlib.pyplot as plt
import torch
from torchvision.models.segmentation import deeplabv3_resnet101
from torchvision import transforms
from concurrent.futures import ThreadPoolExecutor

def detourer_image(image_path):
    img = cv2.imread(image_path)

    if img is None:
        print(f"Error: Unable to open or read the image at {image_path}")
        return

    img = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)
    transformation = transforms.Compose([
        transforms.ToTensor(),
        transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225]),
    ])
    tenseur_entree = transformation(img).unsqueeze(0).to(device)

    with torch.no_grad():
        sortie = deeplab(tenseur_entree)['out'][0]
    predictions_sortie = sortie.argmax(0).cpu().numpy()

    mask = np.zeros_like(img)
    mask[predictions_sortie == 0] = [0, 255, 0]  # Set non-segmented areas to green

    img_with_mask = cv2.addWeighted(img, 1, mask, 0.5, 0)

    cv2.imwrite(image_path, cv2.cvtColor(img_with_mask, cv2.COLOR_RGB2BGR))

def make_deeplab(device):
    deeplab = deeplabv3_resnet101(pretrained=True, progress=True).to(device)
    deeplab.eval()
    return deeplab

def segmenter_image(deeplab, image_paths, output_paths):
    for in_path, out_path in zip(image_paths, output_paths):
        img = cv2.imread(in_path)

        if img is None:
            print(f"Error: Unable to open or read the image at {in_path}")
            continue

        img = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)
        transformation = transforms.Compose([
            transforms.ToTensor(),
            transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225]),
        ])
        tenseur_entree = transformation(img).unsqueeze(0).to(device)

        with torch.no_grad():
            sortie = deeplab(tenseur_entree)['out'][0]
        predictions_sortie = sortie.argmax(0).cpu().numpy()

        mask = np.zeros_like(img)
        mask[predictions_sortie == 0] = [0, 255, 0]  # Set non-segmented areas to green

        img_with_mask = cv2.addWeighted(img, 1, mask, 0.5, 0)

        cv2.imwrite(out_path, cv2.cvtColor(img_with_mask, cv2.COLOR_RGB2BGR))

def decoupe_video(video_path):
    # Répertoire de sortie pour les images extraites
    output_directory = '/Users/raphael.aegerter/Desktop'
    os.makedirs(output_directory, exist_ok=True)

    # Ouvrir la vidéo
    cap = cv2.VideoCapture(video_path)

    # Vérifier si la vidéo est ouverte correctement
    if not cap.isOpened():
        print("Erreur: Impossible d'ouvrir la vidéo.")
        exit()

    # Lire et enregistrer chaque image
    frame_count = 0
    while True:
        ret, frame = cap.read()

        if not ret:
            break

        # Enregistrer l'image dans le répertoire de sortie
        output_path = os.path.join(output_directory, f'frame_{frame_count:04d}.jpg')
        cv2.imwrite(output_path, frame)

        # Détourer l'image sans remove.bg
        detourer_image(output_path)

        frame_count += 1

    # Libérer la ressource de la vidéo
    cap.release()

    print(f"{frame_count} images extraites et détourées avec succès dans {output_directory}")

# Create the deeplab model
device = torch.device("cpu")
deeplab = make_deeplab(device)

# Example usage with video file path
video_path = '/Users/raphael.aegerter/Downloads/cacamou.mp4'
decoupe_video(video_path)

# Répertoire contenant les images détourées
input_directory = '/Users/raphael.aegerter/Desktop'

# Chemin de sortie pour la vidéo
output_path = '/Users/raphael.aegerter/Desktop/video_detournee.mp4'

# Créer la vidéo à partir des images détourées
creer_video(input_directory, output_path)
