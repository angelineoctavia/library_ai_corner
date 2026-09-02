<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AiTool;

class AiToolSeeder extends Seeder
{
    public function run(): void
    {
        $tools = [
            ['ai_name' => 'ChatGPT', 'ai_icon' => 'GPT_Logo.png', 'ai_url' => 'https://chat.openai.com'],
            ['ai_name' => 'Elicit', 'ai_icon' => 'Elicit_Logo.png', 'ai_url' => 'https://elicit.com'],
            ['ai_name' => 'Scispace', 'ai_icon' => 'Scispace_Logo.jpg', 'ai_url' => 'https://typeset.io'],
            ['ai_name' => 'Scite', 'ai_icon' => 'Scite_Logo.png', 'ai_url' => 'https://scite.ai'],
            ['ai_name' => 'Perplexity', 'ai_icon' => 'Perplexity_Logo.png', 'ai_url' => 'https://www.perplexity.ai'],
            ['ai_name' => 'Grammarly', 'ai_icon' => 'Grammarly_Logo.png', 'ai_url' => 'https://www.grammarly.com'],
            ['ai_name' => 'Turnitin', 'ai_icon' => 'Turnitin_Logo.png', 'ai_url' => 'https://www.turnitin.com'],
            ['ai_name' => 'Consensus', 'ai_icon' => 'Consensus_Logo.png', 'ai_url' => 'https://consensus.app'],
            ['ai_name' => 'SmartPLS', 'ai_icon' => 'SmartPLS_Logo.jpg', 'ai_url' => 'https://www.smartpls.com'],
            ['ai_name' => 'QuillBot', 'ai_icon' => 'QuillBot_Logo.jpg', 'ai_url' => 'https://quillbot.com'],
        ];

        foreach ($tools as $tool) {
            AiTool::create($tool);
        }
    }
}