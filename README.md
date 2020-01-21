# Saftladen

The "Saftladen" was a University-Hackathon project. The idea was to allow users to register their cups via a RFID token which is mounted on the bottom side. They can then customize their drinks as a mixture between mutliple juices.

This project made the first place.

![alt text](https://www.th-nuernberg.de/fileadmin/_processed_/e/8/csm_IMG_0680_83b1950e2c.jpg "The Team posing with the project")

## Setup

### Raspberry Pi

The Raspberry runs a simple script which reads the RFID sensor and connects to the "API" to fetch the data. It then uses the given information to use the two pumps to fill the cup with the according amount of two juices. It also guides the user via a simple LCD Panel.

![alt text](https://www.th-nuernberg.de/fileadmin/_processed_/d/4/csm_IMG_0682_25f9f8be1b.jpg "The entry page of the website")

### Webserver

The website is used to register and modify RFID tokens, so that the users can customize their drinks online. The server also provide the functionality to give the data for a specific token as json for the Raspberry to fetch.

![alt text](https://www.th-nuernberg.de/fileadmin/_processed_/5/5/csm_IMG_0684_a5ad922c87.jpg "The Raspberry pi as a central component")

### Proof of Concept model

The hardware required was modeled with lego resembling two aqueducts leading to the cup in the center.