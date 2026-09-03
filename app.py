from flask import Flask, request, jsonify
from flask_cors import CORS
import requests
import os
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

app = Flask(__name__)
CORS(app)

# Hugging Face API configuration
HF_API_KEY = os.getenv('HF_API_KEY')
HF_API_URL = "https://api-inference.huggingface.co/models/zai-org/GLM-5.3"

@app.route('/')
def home():
    return jsonify({"message": "Coding Helper Bot is running!"})

@app.route('/api/chat', methods=['POST'])
def chat():
    try:
        data = request.json
        user_message = data.get('message', '')
        
        if not user_message:
            return jsonify({"error": "No message provided"}), 400
        
        # Call Hugging Face API
        headers = {"Authorization": f"Bearer {HF_API_KEY}"}
        payload = {"inputs": user_message}
        
        response = requests.post(HF_API_URL, headers=headers, json=payload)
        
        if response.status_code != 200:
            return jsonify({"error": "Failed to get response from AI"}), 500
        
        result = response.json()
        
        # Extract the generated text
        if isinstance(result, list) and len(result) > 0:
            ai_response = result[0].get('generated_text', 'No response generated')
        else:
            ai_response = result.get('generated_text', 'No response generated')
        
        return jsonify({"response": ai_response})
    
    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)
