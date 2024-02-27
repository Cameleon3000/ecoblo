import numpy as np
import cv2
import matplotlib.pyplot as plt

import torch
from torchvision.models.segmentation import deeplabv3_resnet101
from torchvision import transforms

def make_deeplab(device):
    deeplab = deeplabv3_resnet101(pretrained=True, progress=True).to(device)
    deeplab.eval()
    return deeplab

def segmenter_image(deeplab, chemin_image, device, output_path):
    # Load and preprocess the image
    img = cv2.imread(chemin_image)

    if img is None:
        print(f"Error: Unable to open or read the image at {chemin_image}")
        return

    img = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)
    transformation = transforms.Compose([
        transforms.ToTensor(),
        transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225]),
    ])
    tenseur_entree = transformation(img).unsqueeze(0).to(device)

    # Perform segmentation
    with torch.no_grad():
        sortie = deeplab(tenseur_entree)['out'][0]
    predictions_sortie = sortie.argmax(0).cpu().numpy()

    # Create a mask where non-segmented areas are green
    mask = np.zeros_like(img)
    mask[predictions_sortie == 0] = [0, 255, 0]  # Set non-segmented areas to green

    # Add the mask to the original image
    img_with_mask = cv2.addWeighted(img, 1, mask, 0.5, 0)

    # Save the resulting image
    cv2.imwrite(output_path, cv2.cvtColor(img_with_mask, cv2.COLOR_RGB2BGR))

    # Display the result
    plt.figure(figsize=(12, 6))
    plt.subplot(1, 3, 1)
    plt.imshow(img)
    plt.title('Image Originale')

    plt.subplot(1, 3, 2)
    plt.imshow(predictions_sortie, cmap='jet', alpha=0.7)
    plt.title('Masque de Segmentation')

    plt.subplot(1, 3, 3)
    plt.imshow(img_with_mask)
    plt.title('Image avec le Masque')

    plt.show()

# Create the deeplab model
device = torch.device("cpu")
deeplab = make_deeplab(device)

# Example usage with output file path
chemin_image = '/Users/raphael.aegerter/Desktop/OIP.jpg'
output_path = '/Users/raphael.aegerter/Desktop/OIP2.jpg'
segmenter_image(deeplab, chemin_image, device, output_path)
