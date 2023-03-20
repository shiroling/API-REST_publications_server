## TODO
- [] Ajouter les nombres de likes et les nombres de dyslikes au GET post du modo
- [] Vérifier que likes marche
- [] Vérifier que dislikes marche
- [] 

## Requis du sujet
### Moderator peut :
#### GET

- [x] [x] Consulter n’importe quel article : auteur, date, contenu, liste des utilisateurs ayant liké l’article, nombre total de like, liste des utilisateurs ayant disliké l’article, nombre total de dislike.
- [x] [x] Consulter tout les articles
- [x] [x] Consulter un utilisateur


#### DELETE
- [x] [x] Supprimer n’importe quel article.


### Publisher peut :
#### GET
- [x] [x] Consulter les articles publiés par les autres utilisateurs. Un utilisateur publisher doit
accéder aux informations suivantes relatives à un article : auteur, date de publication,
contenu, nombre total de like, nombre total de dislike.
- [x] [] Consulter ses propres articles.

#### POST
- [x] [x] Poster un nouvel article.

#### PUT
- [x] [x] Modifier les articles dont il est l’auteur.
- [x] [] Liker les articles publiés par les autres utilisateurs.
- [x] [] Disliker les articles publiés par les autres utilisateurs.

#### DELETE
- [x] [x] Supprimer les articles dont il est l’auteur.

### Non authentifié peut :
#### GET
- [x] [x] Consulter les articles existants. Seules les informations suivantes doivent être
disponibles : auteur, date de publication, co
