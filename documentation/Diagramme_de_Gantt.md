# Diagramme de Gantt - Projet Leboncoin Clone (Équipe de 4)

## Planning du projet (8 semaines)

```mermaid
gantt
    title Planning de développement (Équipe de 4)
    dateFormat  YYYY-MM-DD
    axisFormat %W (Semaine %U)
    
    section Conception
    Spécifications techniques      :a1, 2024-01-01, 7d
    Maquettes interfaces          :after a1, 7d
    
    section Développement
    Module utilisateurs (Dev 1)   :2024-01-08, 14d
    Module annonces (Dev 2)       :2024-01-08, 21d
    Module recherche (Dev 3)      :2024-01-15, 14d
    Module messagerie (Dev 4)     :2024-01-22, 14d
    
    section Tests
    Tests unitaires               :2024-01-29, 7d
    Tests d'intégration           :2024-02-05, 7d
    
    section Livraison
    Déploiement                   :2024-02-12, 7d
```

## Répartition des ressources

| Tâche | Responsable | Durée | Équipe |
|-------|-------------|-------|--------|
| **Conception** | | | |
| Spécifications techniques | Chef de projet | 1 semaine | 1 |
| Maquettes interfaces | Designer | 1 semaine | 1 |
| **Développement** | | | |
| Module utilisateurs | Développeur 1 | 2 semaines | 1 |
| Module annonces | Développeur 2 | 3 semaines | 1 |
| Module recherche | Développeur 3 | 2 semaines | 1 |
| Module messagerie | Développeur 4 | 2 semaines | 1 |
| **Tests** | | | |
| Tests unitaires | QA | 1 semaine | 2 |
| Tests d'intégration | QA | 1 semaine | 2 |
| **Livraison** | | | |
| Déploiement | Ops | 1 semaine | 1 |

## Avantages de l'équipe de 4
- Parallélisation des tâches de développement
- Réduction du temps total de 11 à 8 semaines
- Spécialisation par module
- Capacité à mener tests en parallèle du développement

## Notes
- Les 4 développeurs travaillent en parallèle sur différents modules
- Période de tests réduite grâce à l'équipe QA dédiée
- Maintenance des dépendances entre modules
