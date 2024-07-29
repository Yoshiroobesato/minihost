const axios = require('axios');
const sanitize = require('sanitize-filename');

const API_KEY = process.env.YOUTUBE_API_KEY;

module.exports = async (req, res) => {
    if (req.method === 'GET') {
        try {
            const { videoUrl } = req.query;
            const videoId = extractVideoId(videoUrl);
            const videoDetails = await getVideoDetails(videoId);
            const downloadUrl = await getDownloadUrl(videoUrl);

            if (downloadUrl) {
                res.json({ title: videoDetails.snippet.title, downloadUrl });
            } else {
                res.status(404).json({ error: 'No se encontró una URL de descarga.' });
            }
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    } else {
        res.setHeader('Allow', ['GET']);
        res.status(405).end(`Method ${req.method} Not Allowed`);
    }
};

const getVideoDetails = async (videoId) => {
    const response = await axios.get('https://www.googleapis.com/youtube/v3/videos', {
        params: {
            part: 'snippet,contentDetails',
            id: videoId,
            key: API_KEY
        }
    });
    return response.data.items[0];
};

const getDownloadUrl = async (videoUrl) => {
    const response = await axios.get('https://sitecteatemacro.000webhostapp.com/downloader.php', {
        params: { url: videoUrl }
    });
    return response.data.video_url;
};

const extractVideoId = (url) => {
    const longUrlPattern = /(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/;
    const shortUrlPattern = /(?:https?:\/\/)?(?:www\.)?youtu\.be\/([a-zA-Z0-9_-]+)/;

    let match = url.match(longUrlPattern);
    if (match) {
        return match[1];
    }

    match = url.match(shortUrlPattern);
    if (match) {
        return match[1];
    }

    throw new Error("No se pudo extraer el videoId de la URL.");
};
