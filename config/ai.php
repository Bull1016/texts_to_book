<?php

return [
    'api_key' => env('GEMINI_API_KEY'),
    'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
    'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
    'timeout' => (int) env('GEMINI_TIMEOUT', 120),

    'prompts' => [
        'analysis' => "Tu es une équipe complète composée de : Auteur best-seller, Éditeur professionnel, Correcteur, Expert du sujet, et Illustrateur.
À partir du titre \"{title}\" et de la description \"{subject}\" fournis, réalise le travail suivant :
1. Analyse le sujet.
2. Définis le public cible.
3. Crée une table des matières complète (5 à 7 chapitres).
4. Chaque chapitre doit se terminer par deux sous-sections dédiées : \"Exemples\" et \"Exercices\".
5. Le dernier chapitre doit être une \"Conclusion\" générale.
6. Écris un résumé général.
7. Propose une illustration pour la couverture et une illustration pour chaque chapitre.
8. Pour chaque illustration, génère un prompt optimisé pour une IA génératrice d'images.

Le résultat doit être en {language} et retourné exclusivement au format JSON avec la structure suivante :
{
  \"analysis\": \"...\",
  \"target_audience\": \"...\",
  \"summary\": \"...\",
  \"cover_illustration_prompt\": \"...\",
  \"chapters\": [
    {
      \"title\": \"...\",
      \"description\": \"...\",
      \"illustration_prompt\": \"...\",
      \"subsections\": [
        { \"title\": \"...\", \"description\": \"...\" },
        { \"title\": \"Exemples\", \"description\": \"Exemples concrets liés au chapitre\" },
        { \"title\": \"Exercices\", \"description\": \"Exercices pratiques liés au chapitre\" }
      ]
    }
  ]
}",
        'content' => "Tu es une équipe complète composée de : Auteur best-seller, Éditeur professionnel, Correcteur, Expert du sujet, et Illustrateur.
Tu écris un livre sur le sujet : \"{topic}\".
Voici l'analyse et le plan : {outline}.
Le public cible est : {target_audience}.
Le résumé général est : {summary}.

Écris maintenant le contenu détaillé pour la section \"{title}\" (du chapitre \"{chapter_title}\").
Description de la section : {description}.

Contraintes :
- Style cohérent sur tout le livre.
- Éviter les répétitions.
- Chaque chapitre doit apporter de nouvelles informations.
- Maintenir un niveau professionnel.
- Utiliser le Markdown (gras, listes, etc.).
- Langue : {language}.",
    ],
];
