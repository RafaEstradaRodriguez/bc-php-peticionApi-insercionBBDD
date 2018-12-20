<?php
use GuzzleHttp\Client;
require "vendor/autoload.php";

////////////////////////////////Peticion API//////////////////

//Ejemplo de url:
//$apiUrl = "https://api.themoviedb.org/3/discover/movie?sort_by=popularity.desc&page=4&api_key=6370a7a7c6851cd18cdcbc631f885e9b";

$client = new Client();

$listadoPeliculas = [];
for ($i = 1 ; $i<=40; $i++){
    $response = $client->request("GET", "https://api.themoviedb.org/3/discover/movie?sort_by=popularity.desc&page=".$i."&api_key=6370a7a7c6851cd18cdcbc631f885e9b");
    $variableintermedia = json_decode($response->getBody(),true);
    array_push($listadoPeliculas,$variableintermedia["results"]);
}

echo $listadoPeliculas[0][0]["title"];


////////////Inserción en BBDD///////////

$conn =new mysqli("localhost","root","","The MovieDB");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("INSERT INTO pelicula (vote_count, id, video, vote_average, title, popularity, poster_path,
 original_language, original_title, genre_ids, backdrop_path, adult, overview, release_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
if (!$stmt) {
    echo "Falló la preparación: (" . $conn->errno . ") " . $conn->error;
}

$stmt->bind_param("ssssssssssssss", $vote_count,$id, $video, $vote_average, $title, $popularity, $poster_path,
    $original_language, $original_title, $genre_ids, $backdrop_path, $adult, $overview, $release_date);

$totalpaginas = count($listadoPeliculas);
$pelisPorPagina = count ($listadoPeliculas[0]);
for ( $pagina=0 ; $pagina<=$totalpaginas-1 ; $pagina++ ){
    for ( $peli = 0 ; $peli<=$pelisPorPagina-1 ; $peli++){
        $vote_count = $listadoPeliculas[$pagina][$peli]["vote_count"];
        $id = $listadoPeliculas[$pagina][$peli]["id"];
        $video = $listadoPeliculas[$pagina][$peli]["video"];
        $vote_average = $listadoPeliculas[$pagina][$peli]["vote_average"];
        $title = $listadoPeliculas[$pagina][$peli]["title"];
        $popularity = $listadoPeliculas[$pagina][$peli]["popularity"];
        $poster_path = $listadoPeliculas[$pagina][$peli]["poster_path"];
        $original_language = $listadoPeliculas[$pagina][$peli]["original_language"];
        $original_title = $listadoPeliculas[$pagina][$peli]["original_title"];
        $genre_ids = $listadoPeliculas[$pagina][$peli]["genre_ids"];
        $backdrop_path = $listadoPeliculas[$pagina][$peli]["backdrop_path"];
        $adult = $listadoPeliculas[$pagina][$peli]["adult"];
        $overview = $listadoPeliculas[$pagina][$peli]["overview"];
        $release_date = $listadoPeliculas[$pagina][$peli]["release_date"];
        $stmt->execute();
    }
}

$stmt->close();
$conn->close();