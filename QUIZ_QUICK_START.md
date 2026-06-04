# Quiz Numrique - Dmarrage Rapide

## 
### 1. Accs Admin
- URL: http://127.0.0.1:8000/admin/quizzes
- Login: `admin@example.com` / `password`

### 2. Crer une Question
```
Question: "Combien de continents?"
Rponse: 7
Description: "Des continents habitables"
```
 Question en "draft"

### 3. Activer la Question
 Statut = "active"
- Les joueurs voient la question maintenant

### 4. Les Joueurs Rpondent
- URL: http://127.0.0.1:8000/quiz
- Login: `user@example.com` / `password`
- Soumettre leur rponse
- Voir le classement (scores = 0 pour l'instant)

### 5. Admin Ferme la Question
- Cliquer "Fermer"
 Les scores se recalculent automatiquement!- 
- Les joueurs voient maintenant leurs vrais points

### 6. Voir les Rsultats
- Cliquer "Rsultats"
- Voir le classement avec points et calculs

```## 
Position 1 (plus proche): 5 pts
Position 2: 2 pts
Position 3: 1 pt
Position 4+: 0 pt

Bonus: +3 pts si exacte
```

## 
**Pour les Joueurs:**
- Quiz: `/quiz`
- Profil: `/profile`

**Pour l'Admin:**
- Gestion Quiz: `/admin/quizzes`
- Gestion Utilisateurs: `/admin/users`
- Rsultats: `/admin/quizzes/{id}/results`

## 
### Votre premire partie

 165m km
2. Admin active
3. 4 joueurs rpondent:
 1er + exacte = 8 pts
 2e = 2 pts  
 3e = 1 pt
 4e = 0 pt
4. Admin ferme
5. Total accumul:
   - Alice: 8
   - Bob: 2
   - Carol: 1
   - David: 0

Rptez pour les autres questions!

 Points Importants## 

-  Une seule question active  la fois
-  Chaque joueur rpond qu'une fois par question
-  Les scores s'accumulent entre les questions
-  Les dcimales sont acceptes (165.2, 165.25, etc)
-  Les rponses sont stockes mais les scores = 0 jusqu' fermeture

## 
Lire: `QUIZ_GUIDE.md` pour le guide complet
Lire: `QUIZ_IMPLEMENTATION.md` pour les dtails techniques
