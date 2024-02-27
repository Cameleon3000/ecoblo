import gnupg

def encrypt_message(message, recipient_key_path, sender_key_path, passphrase):
    gpg = gnupg.GPG(gnupghome='/path/to/gnupg/directory')

    with open(recipient_key_path, 'r') as key_file:
        recipient_key_data = key_file.read()

    encrypted_data = gpg.encrypt(message, recipients=recipient_key_data, always_trust=True, sign=sender_key_path, passphrase=passphrase)

    return str(encrypted_data)

# Exemple d'utilisation
message_to_encrypt = "Hello, this is a secret message!"
recipient_key_path = '/path/to/recipient/public/key.asc'
sender_key_path = '/path/to/sender/private/key.asc'
passphrase = 'your_passphrase'

encrypted_message = encrypt_message(message_to_encrypt, recipient_key_path, sender_key_path, passphrase)
print("Encrypted Message:")
print(encrypted_message)
