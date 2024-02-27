import os

def trier_et_supprimer_png(repertoire):
    # Parcours des fichiers dans le répertoire
    for nom_fichier in os.listdir(repertoire):
        chemin_fichier = os.path.join(repertoire, nom_fichier)

        # Vérification si le chemin est un fichier
        if os.path.isfile(chemin_fichier):
            # Obtention de l'extension du fichier
            _, extension = os.path.splitext(nom_fichier)

            # Vérification si l'extension est ".png"
            #if extension.lower() == '.txt':
                #print(f"Suppression du fichier : {nom_fichier}")
                #os.remove(chemin_fichier)
            if "output" in chemin_fichier and ".mp3" in chemin_fichier:
            	print(f"Suppression du fichier : {nom_fichier}")
            	os.remove(chemin_fichier)
            #else:
                # Déplacer le fichier dans un répertoire spécifique en fonction de son type si nécessaire
                # Vous pouvez personnaliser cette partie selon vos besoins

                # Exemple : déplacer les fichiers dans des sous-répertoires en fonction de leur type
             #   type_repertoire = os.path.join(repertoire, extension[1:].upper() + "_files")
              #  if not os.path.exists(type_repertoire):
               #     os.makedirs(type_repertoire)
                #os.rename(chemin_fichier, os.path.join(type_repertoire, nom_fichier))

# Remplacez 'chemin_du_repertoire' par le chemin du répertoire que vous souhaitez trier et nettoyer
chemin_du_repertoire = '/Users/raphael.aegerter/Music/Music/Media.localized/Music/Unknown\ Artist/Unknown\ Album'
trier_et_supprimer_png(chemin_du_repertoire)
