<template>
    <div class="flex flex-col h-screen">
      <div
        id="space"
        class="flex-grow p-5 border-b border-gray-300 mt-16 overflow-y-auto"
        ref="messageContainer"
      >
        <div class="flex flex-col">
          <div
            v-for="(message, index) in chatMessages"
            :key="index"
            :class="[
              'message',
              message.type === 'user' ? 'user-message' : 'bot-message',
            ]"
            ref="messages"
          >
            <template v-if="message.type === 'bot' && message.isLoading">
              <Loader />
            </template>
            <template v-else-if="message.type === 'bot' && message.recipe">
              <RecipeDisplay :recipe="message.recipe" />
              <button @click="addToFavorites(message.recipe)" class="favorite-button">
                ❤️
              </button>
            </template>
            <template v-else-if="message.type === 'bot' && message.shoppingList">
              <ShoppingListDisplay :shoppingList="message.shoppingList" />
            </template>
            <template v-else>
              {{ message.text }}
            </template>
          </div>
        </div>
      </div>
      <div class="flex m-2 p-3">
        <input
          id="input"
          type="text"
          v-model="userInput"
          @keypress.enter="sendMessage"
          placeholder="Tapez votre message ici..."
          class="flex-grow p-2 border text-black border-gray-300 rounded-lg mr-2"
        />
        <button @click="sendMessage" class="p-2 rounded-lg bg-blue-600 text-white cursor-pointer ml-2">
          Envoyer
        </button>
        <button @click="requestShoppingList" class="p-2 rounded-lg bg-green-600 text-white cursor-pointer ml-2">
          Demander une liste de courses
        </button>
      </div>
    </div>
  </template>
  
  <script>
  import { openai } from "../api/openai.js";
  import RecipeDisplay from "./RecipeDisplay.vue";
  import ShoppingListDisplay from "./ShoppingListDisplay.vue";
  import Loader from "./Loader.vue";
  import axios from "axios";
  
  const prompt = `
      Contexte : Je suis un chatbot spécialisé dans la cuisine, amical et compétent. Je suis là pour vous aider avec des recettes, des conseils culinaires et même pour générer des listes de courses basées sur les recettes précédemment discutées dans notre conversation.
  
      Objectif : Fournir des instructions détaillées sur la préparation des recettes, des recommandations d'ingrédients et, sur demande, générer des listes de courses personnalisées en fonction des recettes discutées.
  
      Règles :
      1. Répondre aux questions sur les recettes et générer des recettes sur demande.
      2. Fournir des instructions claires et détaillées sur les étapes de préparation.
      3. Générer des listes de courses basées sur les recettes discutées précédemment.
      4. Adapter les recettes en fonction des préférences alimentaires ou des restrictions spécifiées.
      5. Si une question posée n'est pas sur le thème de la cuisine, alors je répond 'Désolé je ne peux répondre à cette question, elle ne concerne pas le domaine de la cuisine'
      6. Si une question dans une autre langue que le français est posée, alors l'IA répond dans la langue de la question.
      7. L'IA peut se permettre de répondre aux questions du style 'salut, comment ça va ?, l'IA est amicale et peut engager une discussion amicale avec le user.
  
      Critères :
      1. L'IA doit répondre aux demandes de recettes basées sur les ingrédients spécifiés.
      2. Fournir des détails complets sur les étapes de cuisson et les temps nécessaires.
      3. Générer des listes de courses sur demande, en se basant sur les recettes précédemment discutées.
  
      Format de réponse attendu pour une recette :
      \`\`\`json
      {
          "titre": "Titre de la recette",
          "ingredients": [
              "Ingrédient 1",
              "Ingrédient 2",
              "Ingrédient 3",
              ...
          ],
          "etapes": [
              "Étape 1",
              "Étape 2",
              "Étape 3",
              ...
          ]
      }
      \`\`\`
  
      Format de réponse attendu pour une liste de courses :
      \`\`\`json
      {
          "liste_de_courses": [
              "Ingrédient 1",
              "Ingrédient 2",
              "Ingrédient 3",
              ...
          ]
      }
      \`\`\`
  `;
  
  export default {
    components: {
      RecipeDisplay,
      ShoppingListDisplay,
      Loader,
    },
    data() {
      return {
        userInput: "",
        chatMessages: [],
        conversationContext: [],
        lastRequestTime: 0,
        userLanguage: "fr",
        favoriteRecipes: [],
        messageHistory: [],
      };
    },
  
    created() {
      const storedMessages = localStorage.getItem("messageHistory");
      if (storedMessages) {
        this.messageHistory = JSON.parse(storedMessages);
      }
  
      const storedChat = localStorage.getItem("chatMessages");
      if (storedChat) {
        this.chatMessages = JSON.parse(storedChat);
      }
    },
  
    watch: {
      messageHistory: {
        handler(newMessages) {
          localStorage.setItem("messageHistory0", JSON.stringify(newMessages));
          this.saveConversation();
        },
        deep: true,
      },
      chatMessages: {
        handler(newMessages) {
          localStorage.setItem("chatMessages", JSON.stringify(newMessages));
        },
        deep: true,
      },
    },
  
    methods: {
      async sendMessage() {
        if (this.userInput.trim() === "") return;
  
        const now = Date.now();
        const timeSinceLastRequest = now - this.lastRequestTime;
        const minInterval = 1000;
  
        if (timeSinceLastRequest < minInterval) {
          console.warn("Trop de requêtes. Veuillez attendre.");
          return;
        }
        
        this.messageHistory.push({
          id: 0,
        });

        this.messageHistory.push({
          role: "user",
          content: this.userInput,
        });
  
        this.chatMessages.push({
          text: this.userInput,
          type: "user",
        });
  
        this.conversationContext.push({
          role: "user",
          content: this.userInput,
        });
  
        this.scrollToBottom();
  
        const input = this.userInput;
        this.userInput = "";
  
        const loaderMessage = {
          type: "bot",
          isLoading: true,
        };
        this.chatMessages.push(loaderMessage);
        this.scrollToBottom();
  
        if (this.containsShoppingListKeywords(input)) {
          await this.requestShoppingList(loaderMessage);
        } else {
          await this.fetchAIResponse(input, loaderMessage);
        }
  
        setTimeout(() => {
          this.lastRequestTime = Date.now();
        }, 5000);
      },
  
      containsShoppingListKeywords(message) {
        const keywords = ["liste de course", "courses", "liste des courses"];
        const lowerCaseMessage = message.toLowerCase();
  
        return keywords.some((keyword) =>
          lowerCaseMessage.includes(keyword)
        );
      },
  
      async requestShoppingList(loaderMessage) {
        const input = "Générer une liste de courses basée sur les recettes précédentes.";
  
        try {
          const finalResult = await openai.chat.completions.create({
            model: "gpt-3.5-turbo",
            temperature: 0.7,
            messages: [
              {
                role: "system",
                content: prompt,
              },
              ...this.conversationContext,
              {
                role: "user",
                content: input,
              },
            ],
          });
  
          this.handleAIResponse(finalResult, loaderMessage);
        } catch (error) {
          console.error("Erreur lors de la récupération de la liste de courses:", error);
          this.handleError(loaderMessage);
        }
      },
  
      async fetchAIResponse(input, loaderMessage) {
        try {
          const finalResult = await openai.chat.completions.create({
            model: "gpt-3.5-turbo",
            temperature: 0.7,
            messages: [
              {
                role: "system",
                content: prompt,
              },
              ...this.conversationContext,
              {
                role: "user",
                content: input,
              },
            ],
          });
  
          this.handleAIResponse(finalResult, loaderMessage);
        } catch (error) {
          console.error("Erreur lors de la récupération de la réponse de l'IA:", error);
          this.handleError(loaderMessage);
        }
      },
  
      handleAIResponse(finalResult, loaderMessage) {
    let resultText = finalResult.choices[0].message.content;

    // Nettoyer le formatage ```json si présent
    if (resultText.includes("```json") && resultText.includes("```")) {
        resultText = resultText.replace(/```json|```/g, "").trim();
    }

    let parsedResponse;
    try {
        // Essayer de parser la réponse comme JSON
        parsedResponse = JSON.parse(resultText);
    } catch (e) {
        console.error("Erreur lors du parsing JSON:", e);
        parsedResponse = { text: resultText };  // Si le parsing échoue, on garde le texte brut
    }

    // Supprimer le message de chargement
    const index = this.chatMessages.indexOf(loaderMessage);
    if (index !== -1) {
        this.chatMessages.splice(index, 1);
    }

    this.messageHistory.push({
        role: "assistant",
        content: resultText,
    });

    // Vérification si la réponse est une recette
    if (parsedResponse.titre && parsedResponse.ingredients && parsedResponse.etapes) {
        // Si c'est une recette, l'envoyer au composant RecipeDisplay avec le bon format
        this.chatMessages.push({
            recipe: parsedResponse, // On envoie l'objet recette au format attendu par RecipeDisplay
            type: "bot",
        });
    } else {
        // Sinon, l'afficher comme du texte brut
        this.chatMessages.push({
            text: parsedResponse.text || resultText,
            type: "bot",
        });
    }

    this.scrollToBottom();
    this.saveConversation();
}
,
  
      handleError(loaderMessage) {
        const index = this.chatMessages.indexOf(loaderMessage);
        if (index !== -1) {
          this.chatMessages.splice(index, 1);
        }
  
        this.chatMessages.push({
          text: "Désolé, il y a eu une erreur. Veuillez réessayer.",
          type: "bot",
        });
  
        this.scrollToBottom();
      },
  
      scrollToBottom() {
        this.$nextTick(() => {
          const messageContainer = this.$refs.messageContainer;
          messageContainer.scrollTop = messageContainer.scrollHeight;
        });
      },
  
      addToFavorites(recipe) {
        console.log(recipe);
        if (!this.favoriteRecipes.some((favRecipe) => favRecipe.titre === recipe.titre)) {
          this.favoriteRecipes.push(recipe);
          localStorage.setItem("favoriteRecipes", JSON.stringify(this.favoriteRecipes));
          axios.post('/favorites', { recipe })
            .then(response => {
                alert(response.data.message);
            })
            .catch(error => {
                console.error("There was an error adding the recipe to favorites: ", error);
                alert("Une erreur est survenue lors de l'ajout aux favoris.");
            });
        } else {
          alert("Cette recette est déjà dans vos favoris.");
        }
      },
  
      newConversation() {
        this.chatMessages = [];
        this.conversationContext = [];
        this.userInput = "";
        this.saveConversation();
      },
  
      saveConversation() {

        localStorage.setItem("chatMessages", JSON.stringify(this.chatMessages));
      },
    },
  };
  </script>
  
  <style scoped>
  #input {
    color: black;
  }
  .dark-mode .message {
    max-width: 75%;
    background-color: #475977;
    padding: 8px;
    margin: 4px;
    border-radius: 0.5rem;
    word-break: break-word;
  }
  .message {
    max-width: 75%;
    background-color: #d1d5db;
    padding: 8px;
    margin: 4px;
    border-radius: 0.5rem;
    word-break: break-word;
  }
  .bot-message {
    text-align: left;
    align-self: flex-start;
  }
  .user-message {
    text-align: right;
    align-self: flex-end;
  }
  .favorite-button {
    background: none;
    border: none;
    color: red;
    font-size: 24px;
    cursor: pointer;
    margin-top: 10px;
  }
  </style>
  