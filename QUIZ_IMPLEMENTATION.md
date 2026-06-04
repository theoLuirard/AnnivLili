# Systme de Quiz Numrique - Dtails d'Implmentation

Syst## me complet de quiz o         l'admin pose des questions avec rponses numriques, gre l'activation/fermeture des questions, et les joueurs rpondent avec scoring automatique bas sur la proximit.

## 
### Tables Cres

#### `numeric_quizzes` - Questions
```
id (PK)
question (TEXT) - La question pose
correct_answer (DECIMAL 15,2) - La bonne rponse numrique
description (TEXT, nullable) - Contexte/indications
status (ENUM: draft/active/closed) - tat de la question
created_by (FK -> users.id) - Admin qui a cr
timestamps
```

#### `quiz_responses` - Rponses des joueurs
```
id (PK)
quiz_id (FK -> numeric_quizzes.id) - Question
user_id (FK -> users.id) - Joueur
numeric_answer (DECIMAL 15,2) - Rponse fournie
score (INTEGER) - Points gagns (0 jusqu' fermeture)
timestamps
UNIQUE(quiz_id, user_id) - Un joueur, une rponse par question
```

#### `quiz_scores` - Scores cumulatifs
```
id (PK)
user_id (FK -> users.id, UNIQUE) - Joueur
total_score (INTEGER) - Score total de toutes les questions
timestamps
```

## 
### NumericQuiz
- Relations: `creator()`, `responses()`
- Mthodes: `isActive()`, `isClosed()`, `isDraft()`

### QuizResponse
- Relations: `quiz()`, `user()`
- Mthodes: `calculateScore()` - Calcule le score bas sur la proximit

### QuizScore
- Relations: `user()`
- Mthodes:
  - `updateScore(userId)` - Met  jour le score total d'un joueur
  - `getLeaderboard()` - Retourne le classement tri

## 
### Scoring Dtaill
```
Classement par proximit (diffrence absolue avec la bonne rponse)
- 1er le plus proche: 5 points
- 2e le plus proche: 2 points
- 3e le plus proche: 1 point
- 4e et aprs: 0 point

Bonus
+ 3 points si rponse exacte (s'ajoute au classement)
```

### Exemple Concret
Question: "Population de Paris?" (rponse: 2161000)
 1er + exacte = 5 + 3 = **8 pts**
 2e = **2 pts**
 3e = **1 pt**
 4e = **0 pt**

Total aprs question 1:
- Alice: 8
- Bob: 2
- Carol: 1
- David: 0

### Recalcul des Scores
Quand l'admin **ferme une question**, le systme:
1. Rcupre toutes les rponses pour cette question
2. Les trie par proximit (distance  la bonne rponse)
3. Attribue les points selon le classement
4. Met  jour `quiz_responses.score`
5. Met  jour `quiz_scores.total_score` pour chaque joueur

## 
### QuizAdminController
**Routes admin** (`/admin/quizzes/*`)
- `index()` - Liste toutes les questions
- `create()` / `store()` - Crer une nouvelle question
- `edit()` / `update()` - Modifier une question (draft seulement)
- `activate()` - Activer une question (ferme les autres)
- `close()` - Fermer une question et recalculer les scores
- `destroy()` - Supprimer une question
- `showResults()` - Afficher les rsultats et classement

### QuizController
**Routes joueur** (`/quiz*`)
- `show()` - Affiche la question active (s'il y en a) + classement
- `submit()` - Soumettre une rponse numrique

## 
### Admin
- `/admin/quizzes` - `admin/quizzes/index.blade.php`
  - Liste des questions avec actions (activer, fermer, diter, voir rsultats)
  
- `/admin/quizzes/create` - `admin/quizzes/create.blade.php`
  - Formulaire cration (rutilis aussi pour dition)
  
- `/admin/quizzes/{id}/edit` - `admin/quizzes/edit.blade.php`
  - Formulaire dition
  
- `/admin/quizzes/{id}/results` - `admin/quizzes/results.blade.php`
  - Classement dtaill avec calcul de diffrence et points

### Joueur
- `/quiz` - `quiz/show.blade.php`
  - Affiche la question active (s'il y en a)
  - Formulaire de rponse
  - Classement global cumulatif dans la sidebar

## 
### Routes Admin
- Middleware: `auth` + `role:admin`
- Protection: Seuls les admins peuvent crer/modifier/grer les questions

### Routes Joueur
- Middleware: `auth`
- Protection: Tous les utilisateurs connects peuvent jouer

### Validations
- `numeric_answer` requis et doit .editorconfig .env .env.example .gitattributes .gitignore .npmrc FEATURES.md IMPLEMENTATION_SUMMARY.md QUIZ_GUIDE.md README.md SETUP_GUIDE.md app artisan bootstrap composer.json composer.lock config database node_modules package-lock.json package.json phpunit.xml public resources routes storage tests vendor vite.config.js tre numrique
- `question` requis et max 500 caractres
- `correct_answer` requis et numrique
- `quiz_id` et `user_id` valids via FK

## 
```
Admin cre question (draft)
        
Admin active question
        
Question passe  "active"
(Anciennes questions: "closed")
        
Joueurs voient la question
        
Joueurs soumettent rponses
        
Rponses stockes avec score=0
        
Admin clique "Fermer"
        
Systme recalcule scores:
- Trie les rponses par proximit
- Attribue les points (5,2,1,0 +3 bonus)
- Met  jour quiz_scores.total_score
        
Joueurs voient scores updats
```

## 
Trois questions exemple prcharges en statut "draft":
 193
 8849
 1969

Comptes test:
- Admin: admin@example.com / password
- User: user@example.com / password

## 
 **Unique par joueur/question**: Chaque joueur ne peut rpondre qu'une fois par question
 **Scores cumulatifs**: Les points s'accumulent entre les questions
 **Une seule question active**: Activation d'une nouvelle ferme l'ancienne
 **Recalcul automatique**: Fermeture d'une question recalcule tous les scores
 **Dcimales acceptes**: Les rponses peuvent .editorconfig .env .env.example .gitattributes .gitignore .npmrc FEATURES.md IMPLEMENTATION_SUMMARY.md QUIZ_GUIDE.md README.md SETUP_GUIDE.md app artisan bootstrap composer.json composer.lock config database node_modules package-lock.json package.json phpunit.xml public resources routes storage tests vendor vite.config.js tre 8849.50, etc.
 **Classement en temps rel**: Les joueurs voient le classement mis  jour

## 
- Pas de limite de temps par question
- Pas de notification en temps rel
- Pas d'historique des questions fermes
- Pas de rinitialisation des scores entre cycles
- Pas d'export des rsultats

---

**Date**: 4 juin 2026
**Statut Complet et fonctionnel**: 
