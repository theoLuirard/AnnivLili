# Système de Quiz Numérique - Guide d'Utilisation

## Vue d'ensemble
Un système interactif de quiz où l'admin pose des questions avec réponses numériques, les joueurs répondent, et les classements se calculent automatiquement selon la proximité de la bonne valeur.

## Accès

### Pour les Joueurs (tous les utilisateurs)
- URL: `/quiz`
- Voir la question active (s'il y en a une)
- Soumettre une réponse numérique
- Voir le classement global cumulatif
- Les scores s'accumulent d'une manche à l'autre

### Pour l'Admin
- URL: `/admin/quizzes`
- Créer des questions en brouillon
- Gérer les questions (éditer, supprimer)
- Activer une question pour la rendre visible aux joueurs
- Fermer une question pour arrêter les réponses
- Voir les résultats et le classement

## Flux de Jeu

### Créer une Question (Admin)
1. Aller à `/admin/quizzes`
2. Cliquer sur "Nouvelle Question"
3. Remplir:
   - **Question**: La question à poser
   - **Réponse Correcte**: Le bon nombre (accepte décimales)
   - **Description** (optionnel): Indications ou contexte
4. Cliquer "Créer"
5. La question est en statut "draft"

### Jouer une Manche (Admin + Joueurs)
1. **Admin active la question**:
   - Va à `/admin/quizzes`
   - Clique sur "Activer" pour la question
   - La question passe au statut "active"
   - Note: Une seule question peut être active à la fois

2. **Les joueurs répondent**:
   - Vont à `/quiz`
   - Voient la question active
   - Entrent leur réponse numérique
   - Voient leur réponse enregistrée
   - Consultent le classement (pas de scores affichés encore)

3. **Admin ferme la question**:
   - Va à `/admin/quizzes`
   - Clique sur "Fermer" pour la question
   - Les scores sont **recalculés automatiquement**
   - Les joueurs voient maintenant les scores sur le classement

### Voir les Résultats (Admin)
1. Va à `/admin/quizzes`
2. Pour une question fermée, clique sur "Résultats"
3. Voit:
   - La réponse correcte
   - Nombre de réponses
   - Classement détaillé avec:
     - Position (1er, 2e, 3e)
     - Nom du joueur
     - Réponse donnée
     - Différence avec la bonne réponse
     - Points gagnés

## Système de Scoring

### Points attribués à chaque question

| Classement | Points |
|-----------|--------|
| 1er le plus proche | 5 pts |
| 2e le plus proche | 2 pts |
| 3e le plus proche | 1 pt |
| 4e et après | 0 pt |

### Bonus
- **+3 points** si la réponse est **exacte** (s'ajoute au classement)

### Exemples

**Question**: "Quel est le plus haut sommet du monde?" (réponse: 8849)
- Joueur A répond 8849 → 1er ET exacte → 5 + 3 = **8 points**
- Joueur B répond 8850 → 2e → **2 points**
- Joueur C répond 8800 → 3e → **1 point**
- Joueur D répond 8000 → 4e → **0 point**

**Points cumulatifs**:
- Après 3 questions, tous les joueurs auront accumulé leurs points
- Les scores s'affichent dans le classement global
- Les scores restent d'une question à l'autre

## Navigation

### Barre de Navigation

**Pour tous les utilisateurs**:
- 🏠 Dashboard
- 🎮 Quiz
- 👤 My Profile
- 🚪 Logout

**Pour l'admin (en plus)**:
- 👥 Admin Panel (gestion utilisateurs)
- 📝 Manage Quiz (gestion des questions)

## Données de Test

### Comptes de test prêts à utiliser
```
Admin:
- Email: admin@example.com
- Password: password

User:
- Email: user@example.com
- Password: password
```

### Questions de test déjà créées (en statut "draft")
1. "Combien de pays sont membres de l'ONU?" → Réponse: 193
2. "Quel est le plus haut sommet du monde en mètres?" → Réponse: 8849
3. "En quel année l'homme a marché sur la lune?" → Réponse: 1969

## Cas d'Usage

### Scénario 1: Quiz en Direct
1. Admin crée des questions en avance (plusieurs brouillons)
2. Pendant l'événement, l'admin active une question
3. Les joueurs voient la question et répondent en direct
4. Admin ferme la question quand le temps est écoulé
5. Les scores s'affichent automatiquement
6. Répétez pour chaque question

### Scénario 2: Quiz Étalé dans le Temps
1. Admin crée une question le matin
2. L'active pour laisser les joueurs répondre toute la journée
3. La ferme le soir pour voir les résultats
4. Les points s'accumulent avec les questions suivantes

## Notes Importantes

- **Une seule question active à la fois**: Quand vous en activez une nouvelle, la précédente est fermée
- **Pas de modification après réponses**: Les questions en statut "active" ou "closed" ne doivent pas être modifiées
- **Les réponses sont uniques par joueur**: Chaque joueur ne peut répondre qu'une fois par question
- **Les scores restent cumulatifs**: Les points ne se réinitialisent pas entre les questions
- **Les décimales sont acceptées**: Vous pouvez avoir des réponses comme 8849.50

## Améliorations Futures (optionnel)

- ✅ Système de notification en temps réel
- ✅ Export des résultats en CSV
- ✅ Historique des questions
- ✅ Photos/vidéos dans les descriptions
- ✅ Timers pour chaque question
- ✅ Réinitialisation des scores
- ✅ Catégories de questions

---

**Créé**: 4 juin 2026
**Statut**: ✅ Opérationnel
