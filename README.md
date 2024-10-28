## Usage

To get started, make sure you have [Docker installed](https://docs.docker.com/docker-for-mac/install/) on your system, and then clone this repository.

Next, navigate in your terminal to the directory you cloned this, and spin up the containers for the web server:
`docker-compose up --build`.

Next, create few tables and feed with sample data:
`docker exec app php artisan migrate:fresh --seed`

Go to your browser. Hopefully you'll see the app running at:
`http://127.0.0.1:8000`


## Conqurent Buyers App

You can experiment with different basket, wallets and product price/quantity combinations. Just enter values in appropriate fields and press "BUY NOW" to simulate conqurent buyers case. Once you feel bored, you can reset to the initial task state by pressing "RESET" button.