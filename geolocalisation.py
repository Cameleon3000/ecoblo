import tkinter as tk
from tkinter import ttk
import requests
import webbrowser
import folium

class GeolocationApp(tk.Tk):
    def __init__(self):
        super().__init__()
        self.title("ipstack Geolocation")

        # Ajout d'un champ de saisie pour l'adresse IP
        ip_label = ttk.Label(self, text="Adresse IP:")
        ip_label.grid(row=0, column=0, padx=10, pady=10, sticky="w")

        self.ip_entry = ttk.Entry(self)
        self.ip_entry.grid(row=0, column=1, padx=10, pady=10)

        # Bouton pour obtenir la géolocalisation
        geolocate_button = ttk.Button(self, text="Obtenir Géolocalisation", command=self.get_geolocation)
        geolocate_button.grid(row=1, column=0, columnspan=2, pady=10)

        # Zone de texte pour afficher les résultats
        self.result_text = tk.StringVar()
        result_label = ttk.Label(self, textvariable=self.result_text, wraplength=400, justify="left")
        result_label.grid(row=2, column=0, columnspan=2, padx=10, pady=10)

    def get_geolocation(self):
        api_key = '37b5e00a5c5eedbe8eb846f5690794e6'
        ip_address = self.ip_entry.get()

        url = f'http://api.ipstack.com/{ip_address}?access_key={api_key}'

        try:
            response = requests.get(url)
            data = response.json()

            if 'country_name' in data:
                self.result_text.set(f"Pays: {data['country_name']}\nVille: {data.get('city', 'N/A')}\nLatitude: {data.get('latitude', 'N/A')}, Longitude: {data.get('longitude', 'N/A')}")

                # Créer la carte et ajouter un marqueur
                map_location = folium.Map(location=[data.get('latitude', 0), data.get('longitude', 0)], zoom_start=10)
                folium.Marker([data.get('latitude', 0), data.get('longitude', 0)], popup=data['city']).add_to(map_location)

                # Sauvegarder la carte en tant que fichier HTML
                map_location.save('/Users/raphael.aegerter/Desktop/Anniv_Joel/map.html')

                # Afficher l'emplacement du fichier dans la console pour le débogage
                print(f"Carte sauvegardée à l'emplacement : /Users/raphael.aegerter/Desktop/Anniv_Joel/map.html")

                # Ouvrir la carte dans le navigateur par défaut
                webbrowser.open('file://' + '/Users/raphael.aegerter/Desktop/Anniv_Joel/map.html')

            else:
                self.result_text.set("Données de géolocalisation non disponibles.")
        except requests.RequestException as e:
            self.result_text.set(f"Erreur lors de la requête : {e}")

    def applicationSupportsSecureRestorableState(self):
        return True

# Création de l'instance de l'application
app = GeolocationApp()

# Exécution de la boucle principale Tkinter
app.mainloop()
