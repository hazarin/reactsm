FROM node:14 AS deps

#RUN apt install --no-cache libc6-compat && rm -rf /var/lib/apt/lists/*
WORKDIR /app
COPY package.json yarn.lock ./
RUN yarn install --frozen-lockfile --network-timeout 1000000

FROM node:14 AS builder
WORKDIR /app
COPY --from=deps /app/ ./
COPY ./assets ./assets
COPY webpack.config.js .
COPY .babelrc .
COPY .env .
RUN yarn build

FROM php:7.4 AS runner
WORKDIR /app

RUN apt update && apt install zip unzip git curl -y && rm -rf /var/lib/apt/lists/*
#RUN wget https://get.symfony.com/cli/installer -O - | bash

COPY --from=builder /app/public ./public
COPY ./bin ./bin
COPY ./config ./config
COPY ./migrations ./migrations
COPY ./public/index.php ./public/
COPY ./src ./src
COPY ./templates ./templates
COPY ./translations ./translations
COPY .env .
COPY composer.json .
COPY composer.lock .

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=symfonycorp/cli symfony /usr/local/bin/symfony
RUN composer install
#RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

RUN ./bin/console doctrine:database:create -n
RUN ./bin/console doctrine:migrations:migrate -n
RUN ./bin/console doctrine:fixtures:load -n
RUN ./bin/console assets:install --symlink -n
RUN ./bin/console lexik:jwt:generate-keypair --skip-if-exists
RUN symfony server:ca:install

EXPOSE 8000

CMD ["symfony", "server:start"]
