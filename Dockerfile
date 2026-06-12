FROM bitnami/laravel:10

# Copier les fichiers du projet dans le conteneur
COPY . /app

# Donner les droits d'écriture pour Laravel
USER root
RUN chown -R bitnami:bitnami /app/storage /app/bootstrap/cache

# Revenir à l'utilisateur par défaut
USER bitnami

EXPOSE 8000
