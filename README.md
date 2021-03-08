## Setup

To start the docker run the following command in root project 
```bash
docker-compose up -d --build
```
Migrate the database
```bash
docker-compose exec neo4j cypher-shell -u neo4j -p abcde -f /opt/project/microservices.cypher
```