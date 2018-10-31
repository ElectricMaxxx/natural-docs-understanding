# Document Parsing through NLU

This code will run through a given documentation and parse it through a [Rasa NLU](https://rasa.com/docs/getting-started/overview/) and [training data](app/data/simple_docs.md).

# Preparing

Clone it:

```bash

git clone git@github.com:ElectricMaxxx/natural-docs-understanding.git
cd natural-docs-understanding/
cd client-app
compose install
cd ..

```

Run it in docker:

```bash

docker-compose up -d

```

Possible edit or add configuration in `app/data/`

Clone some docs into the docs folder:

```bash

cd docs/
git clone git@github.com:symfony-cmf/symfony-cmf-docs.git

```

Enter app docker:

```bash

docker exec -it rasa-nlu-client
cd /app/src/

```

```bash
bin/console # will show all commands 'rasa:nlu' should interest
```

* train data - `/data/data` contain configuration from `app/data`
* parse file - `/data/docs` contains `docs/`
* parse all `sh bin/parseAll.sh`
* more ..