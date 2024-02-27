import tkinter as tk
from tkinter import PhotoImage

# Créer une fenêtre
fenetre = tk.Tk()
fenetre.title("JOJO")
fenetre.geometry("1250x400")

# Charger l'image initiale
image_path = "/Users/raphael.aegerter/Desktop/Anniv_Joel/Alertbox.png"
image = PhotoImage(file=image_path)

# Créer un widget Label pour afficher l'image
label_image = tk.Label(fenetre, image=image)
label_image.pack()

# Fonction pour changer l'image
def change_image(new_image_path):
    new_image = PhotoImage(file=new_image_path)
    label_image.configure(image=new_image)
    label_image.image = new_image  # Gardez une référence à la nouvelle image
    bouton.destroy()

# Créer un bouton pour changer l'image
bouton = tk.Button(fenetre, text="Joel est trop cool", font=("Helvetica", 24), command=lambda: change_image("/Users/raphael.aegerter/Desktop/Anniv_Joel/Joël.png"))
bouton.place(x=1032, y=320)

# Exécuter la boucle principale
fenetre.mainloop()
