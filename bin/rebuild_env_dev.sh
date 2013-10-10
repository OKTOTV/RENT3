#!/bin/bash

app/console cache:clear --env=dev
app/console doctrine:database:drop --env=dev --force
app/console doctrine:database:create --env=dev
app/console doctrine:schema:create --env=dev

app/console doctrine:fixtures:load --no-interaction --append --env=dev --fixtures src/Oktolab/Bundle/RentBundle/DataFixtures/
app/console faker:populate
