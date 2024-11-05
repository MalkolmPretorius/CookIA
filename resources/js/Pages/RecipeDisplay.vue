<template>
    <div class="recipe-container">
      <h2>Recette : {{ recipe.titre }}</h2>
      <h3>Ingrédients :</h3>
      <ul>
        <li v-for="ingredient in recipe.ingredients" :key="ingredient">{{ ingredient }}</li>
      </ul>
      <h3>Instructions :</h3>
      <ol>
        <li v-for="etape in recipe.etapes" :key="etape">{{ etape }}</li>
      </ol>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      recipe: {
        type: Object,
        required: true,
      },
    },
  };
  </script>
  
  <style>
  /* Ajoutez vos styles ici pour personnaliser l'apparence des recettes */
  .recipe-container h2 {
    color: #007bff;
    font-size: 20px;
    font-weight: bold;

  }
  .recipe-container h3 {
    color: #171717;
    font-weight: bold;
  }
  </style>
  