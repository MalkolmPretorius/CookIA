<template>
    <div id="space" class="flex-grow p-5  border-b border-gray-300 mt-16">
        <div class="flex flex-col" ref="messages">
            <div
                v-for="(message, index) in chatMessages"
                :key="index"
                :class="{'user-message': message.type === 'user', 'bot-message': message.type === 'bot'}"
            >
                {{ message.text }}
            </div>
        </div>  
    </div>
    <div class="flex m-2 p-3">
        <input
        id="input"
            type="text"
            v-model="userInput"
            @keypress.enter="sendMessage"
            placeholder="Type your message here..."
            class="flex-grow p-2 border text-black border-gray-300 rounded-lg mr-2"
        />
        <button
            @click="sendMessage"
            class="p-2 rounded-lg bg-blue-600 text-white cursor-pointer ml-2"
        >
            Send
        </button>
    </div>
</template>

<script>
import {openai} from "../api/openai.js";
export default {
    data() {
        return {
            userInput: "",
            chatMessages: [],
        };
    },
    methods: {
        async sendMessage() {
            if (this.userInput.trim() === "") return;

            // Add user message to the chat
            this.chatMessages.push({
                text: this.userInput,
                type: "user",
            });

            const finalResult = await openai.chat.completions.create({
                model: "gpt-4",
                temperature: 0.7,
                messages: [
                    {
                        role: "system",
                        content: `
                        Context: A chatbot assisting a user with some ingredients for a recipe. The user asks the chatbot for the ingredients needed to make a recipe with the following criteria : ${this.userInput}

                        Goal: Provide the user with the ingredients needed to make the recipe.

                        Rules :
                        If the user asks anything other than the ingredients, the chatbot should respond with the following message: "I'm sorry, I can only provide you with the ingredients needed to make the recipe."

                        Criteria:
                        1. The chatbot should provide the user with the ingredients needed to make the recipe.
                        2. The chatbot take into consideration the user's criteria when providing the ingredients.
                        3. The chatbot should provide the user with the ingredients in a clear and concise manner.`},
                    {
                        role: "user",
                        content: this.userInput,
                    }]
            });

            const resultText = finalResult.data.choices[0].message.content;

            console.log(resultText);
        },
    },
};
</script>

<style scoped>
#input{
    color: black;
}
.bot-message {
    text-align: left;
}

.user-message {
    text-align: right;
}
</style>
