<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $youtube_url = $_POST["url"];
    $video_id = get_video_id($youtube_url);
    if ($video_id) {
        $mp3_file = convert_to_mp3($video_id);
        if ($mp3_file) {
            header("Content-Type: application/octet-stream");
            header("Content-Transfer-Encoding: Binary");
            header("Content-disposition: attachment; filename=\"" . basename($mp3_file) . "\"");
            readfile($mp3_file);
            exit;
        } else {
            echo "Failed to convert video to MP3.";
        }
    } else {
        echo "Invalid YouTube URL.";
    }
}

function get_video_id($url) {
    $parsed_url = parse_url($url);
    if (isset($parsed_url["query"])) {
        parse_str($parsed_url["query"], $query_vars);
        if (isset($query_vars["v"])) {
            return $query_vars["v"];
        }
    }
    return false;
}

function convert_to_mp3($video_id) {
    $cmd = "youtube-dl --extract-audio --audio-format mp3 https://www.youtube.com/watch?v=$video_id";
    exec($cmd, $output, $status);
    if ($status == 0) {
        $mp3_file = glob("$video_id*.mp3")[0];
        return $mp3_file;
    }
    return false;
}
?>
