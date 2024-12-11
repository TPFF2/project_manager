import {Client,GatewayIntentBits } from "discord.js";
import dotenv from 'dotenv';
dotenv.config();
import axios from 'axios';

const LARAVEL_API_ENDPOINT = `${process.env.APP_URL}`;
process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0';


const client = new Client({
    intents: [
        GatewayIntentBits.Guilds,
        GatewayIntentBits.GuildPresences,
    ]
});
let TARGET_USER_ID = null;

async function fetchSanctumToken() {
    try {
        const response = await axios.get(`${LARAVEL_API_ENDPOINT}/api/bot/token`, {
            headers: {
                Authorization: `Bearer bot_admin_token`, // Un token pré-configuré pour authentifier cette requête
            },
        });

        return response.data.token;
    } catch (error) {
        console.error('Erreur lors de la récupération du token Sanctum :', error.message);
        throw error;
    }
}
async function fetchTargetUserId() {
    try {
        const response = await axios.get(`${LARAVEL_API_ENDPOINT}/api/discord/user`, {

        });

        TARGET_USER_ID = response.data.discord_id;
        console.log(`Utilisateur cible : ${TARGET_USER_ID}`);
    } catch (error) {
        console.error('Erreur lors de la récupération de l\'utilisateur Discord:', error.message);
    }
}

client.on('presenceUpdate', async (oldPresence, newPresence) => {
    if (!TARGET_USER_ID) {
        console.error('Aucun utilisateur cible défini.');
        return;
    }

    if (newPresence.userId === TARGET_USER_ID) {
        const status = newPresence.status; // 'online', 'idle', 'dnd', 'offline'
        console.log(`Statut de l'utilisateur cible : ${status}`);

        // Envoyer le statut à Laravel
        try {
            await axios.post(`${LARAVEL_API_ENDPOINT}/api/discord/status`, {
                status,
                user_id: TARGET_USER_ID,
            });
            console.log(`Statut envoyé à Laravel : ${status}`);
        } catch (error) {
            console.error('Erreur lors de l\'envoi du statut :', error.message);
        }
    }
});

// Connecter le bot Discord
client.on('ready', async () => {
    console.log(`Bot connecté en tant que ${client.user.tag}`);
    await fetchTargetUserId(); // Récupère dynamiquement le discord_id
});

client.login(process.env.DISCORD_BOT_TOKEN);

