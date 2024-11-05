// Pas besoin d'utiliser dotenv avec Vite, les variables VITE_ sont automatiquement chargées
import { OpenAI } from "openai";

export const openai = new OpenAI({
  apiKey: import.meta.env.VITE_OPENAI_API_KEY,
  dangerouslyAllowBrowser: true
});
