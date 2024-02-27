import cv2
import numpy as np
import os
import torch
from torchvision.models.segmentation import deeplabv3_resnet101
from torchvision import transforms
from tqdm import tqdm
import time

depart = time.time()

# Fonction pour détouner une image avec le modèle DeepLab
def detourer_image(image_path, output_path):
    img = cv2.imread(image_path)

    if img is None:
        print(f"Erreur : Impossible d'ouvrir ou de lire l'image à {image_path}")
        return

    # Convertir l'image en RGB
    img_rgb = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)

    # Effectuer la segmentation
    transformation = transforms.Compose([
        transforms.ToTensor(),
        transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225]),
    ])
    tenseur_entree = transformation(img_rgb).unsqueeze(0).to(device)

    with torch.no_grad():
        sortie = deeplab(tenseur_entree)['out'][0]
    predictions_sortie = sortie.argmax(0).cpu().numpy()

    # Créer un masque vert solide
    masque_vert = np.zeros_like(img)
    masque_vert[:] = [0, 255, 0]  # Définir l'ensemble du masque en vert

    # Mélanger l'image originale avec le masque vert dans les régions segmentées
    img_avec_masque = img.copy()
    img_avec_masque[predictions_sortie == 0] = masque_vert[predictions_sortie == 0]

    # Enregistrer l'image résultante
    cv2.imwrite(output_path, cv2.cvtColor(img_avec_masque, cv2.COLOR_RGB2BGR))


# Fonction pour créer le modèle DeepLab
def make_deeplab(device):
    deeplab = deeplabv3_resnet101(pretrained=True, progress=True).to(device)
    deeplab.eval()
    return deeplab

# Fonction pour segmenter plusieurs images
def segmenter_images(deeplab, chemins_images, chemins_sorties):
    for chemin_entree, chemin_sortie in zip(chemins_images, chemins_sorties):
        img = cv2.imread(chemin_entree)

        if img is None:
            print(f"Erreur : Impossible d'ouvrir ou de lire l'image à {chemin_entree}")
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

        masque = np.zeros_like(img)
        masque[predictions_sortie == 0] = [0, 255, 0]  # Définir les zones non segmentées en vert

        img_avec_masque = cv2.addWeighted(img, 1, masque, 0.5, 0)

        cv2.imwrite(chemin_sortie, cv2.cvtColor(img_avec_masque, cv2.COLOR_RGB2BGR))

# Fonction pour découper une vidéo en images, les détourner et créer une nouvelle vidéo
def decouper_video(video_path):
    global fps
    # Répertoire de sortie pour les images extraites
    repertoire_sortie = '/Users/raphael.aegerter/Desktop/detourner'
    os.makedirs(repertoire_sortie, exist_ok=True)

    # Ouvrir la vidéo
    cap = cv2.VideoCapture(video_path)

    # Vérifier si la vidéo est ouverte correctement
    if not cap.isOpened():
        print("Erreur : Impossible d'ouvrir la vidéo.")
        exit()

    # Obtenir le nombre total d'images dans la vidéo
    total_images = int(cap.get(cv2.CAP_PROP_FRAME_COUNT))

    # Obtenir la fréquence d'images par seconde (FPS)
    fps = cap.get(cv2.CAP_PROP_FPS)

    # Obtenir la durée totale de la vidéo en secondes
    duree_video_sec = total_images / fps

    print(f"La vidéo a une fréquence d'images de {fps} FPS.")
    print(f"La durée totale de la vidéo est {duree_video_sec} secondes.")

    # Utiliser tqdm pour afficher une barre de progression
    with tqdm(total=total_images, desc="Traitement des images") as barre_progression:
        numero_image = 0
        while True:
            ret, image = cap.read()

            if not ret:
                break

            # Enregistrer l'image dans le répertoire de sortie
            chemin_sortie = os.path.join(repertoire_sortie, f'image_{numero_image:04d}.jpg')
            cv2.imwrite(chemin_sortie, image)

            # Détourer l'image avec le modèle DeepLab
            detourer_image(chemin_sortie, chemin_sortie)

            numero_image += 1
            barre_progression.update(1)

    # Libérer la ressource de la vidéo
    cap.release()

    print(f"{numero_image} images extraites et détourées avec succès dans {repertoire_sortie}")

# Fonction pour créer une vidéo à partir des images détourées
def creer_video(repertoire_entree, chemin_sortie):
    global fps
    fps = fps
    # Récupérer la liste des images dans le répertoire d'entrée
    images = [img for img in os.listdir(repertoire_entree) if img.endswith(".jpg")]
    images.sort()

    # Récupérer les dimensions de la première image
    image = cv2.imread(os.path.join(repertoire_entree, images[0]))
    hauteur, largeur, couches = image.shape

    # Créer l'objet VideoWriter
    video = cv2.VideoWriter(chemin_sortie, cv2.VideoWriter_fourcc(*'mp4v'), fps, (largeur, hauteur))

    # Ajouter chaque image à la vidéo
    for nom_image in images:
        chemin_image = os.path.join(repertoire_entree, nom_image)
        image = cv2.imread(chemin_image)

        # Ajouter l'image détourée à la vidéo
        video.write(image)

    # Libérer la ressource de la vidéo
    video.release()

    print(f"Vidéo créée avec succès à {chemin_sortie}")

# Créer le modèle DeepLab
device = torch.device("cpu")
deeplab = make_deeplab(device)

# Utilisation exemple avec le chemin de la vidéo
chemin_video = '/Users/raphael.aegerter/Desktop/hello.mp4'
decouper_video(chemin_video)

# Répertoire contenant les images détourées
repertoire_entree = '/Users/raphael.aegerter/Desktop/detourner'

# Chemin de sortie pour la nouvelle vidéo
chemin_sortie_video = '/Users/raphael.aegerter/Desktop/video_detournee.mp4'

# Créer la nouvelle vidéo à partir des images détourées
creer_video(repertoire_entree, chemin_sortie_video)

fin = time.time()

temps_total = fin-depart
print(f"Cela a duré ")
