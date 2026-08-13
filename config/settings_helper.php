<?php
// Settings Helper for ITS-BERT Site Customizations

function get_site_settings() {
    $file = __DIR__ . '/site_settings.json';
    $default = [
        'site_name' => 'ITS-BERT',
        'site_subtitle' => 'INTELLIGENT TUTOR',
        'site_tagline' => 'Learn Smarter with AI-Powered Tutoring',
        'hero_badge' => 'BERT NLP Transformer System',
        'hero_title' => 'Learn Smarter with AI-Powered Tutoring',
        'hero_subtitle' => 'An intelligent tutoring system powered by advanced Natural Language Processing to provide personalized learning assistance, instant answers, interactive quizzes, and progress tracking.',
        'logo_icon' => 'bi-cpu-fill',
        'logo_image' => '',
        'hero_bg_image' => 'hero_learning_bg.jpg',
        'footer_text' => 'ITS-BERT is an AI-driven Intelligent Tutoring System designed for Computer Science education.',
        'contact_email' => 'support@itsbert.edu.ng'
    ];

    if (file_exists($file)) {
        $content = file_get_contents($file);
        $data = json_decode($content, true);
        if (is_array($data)) {
            return array_merge($default, $data);
        }
    }
    return $default;
}

function save_site_settings($settings) {
    $file = __DIR__ . '/site_settings.json';
    $current = get_site_settings();
    $merged = array_merge($current, $settings);
    return file_put_contents($file, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}
