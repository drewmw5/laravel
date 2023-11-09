FROM php

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update && apt-get install -y \
    python3-pip

RUN pip install youtube_transcript_api --break-system-packages