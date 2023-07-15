<?php

// Funkcja wyszukiwania
function searchGrid($grid, $searchTerm) {
    $searchResults = [];
    foreach ($grid as $row) {
        foreach ($row as $value) {
            if (stripos($value, $searchTerm) !== false) {
                $searchResults[] = $row;
                break;
            }
        }
    }
    return $searchResults;
}

// Funkcja sortowania
function sortGrid($grid, $sortColumn, $sortOrder = SORT_ASC) {
    $sortedGrid = $grid;
    usort($sortedGrid, function($a, $b) use ($sortColumn, $sortOrder) {
        if (isset($a[$sortColumn]) && isset($b[$sortColumn])) {
            return $sortOrder === SORT_ASC ? $a[$sortColumn] <=> $b[$sortColumn] : $b[$sortColumn] <=> $a[$sortColumn];
        }
        return 0;
    });
    return $sortedGrid;
}

// Funkcja filtrowania
function filterGrid($grid, $filterColumn, $filterValue) {
    $filteredGrid = [];
    foreach ($grid as $row) {
        if (isset($row[$filterColumn]) && $row[$filterColumn] === $filterValue) {
            $filteredGrid[] = $row;
        }
    }
    return $filteredGrid;
}

// Ścieżka do pliku CSV
$csvFile = 'C:\xampp\htdocs\ank.csv';

// Otwieranie pliku CSV
if (($handle = fopen($csvFile, "r")) !== false) {
    // Odczytanie pierwszego wiersza jako nagłówek
    $header = fgetcsv($handle, 1000, ",");

    // Tworzenie pustej tablicy na siatkę
    $grid = [];

    // Pętla przechodząca przez każdą linię pliku CSV
    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        // Dodawanie wiersza do tablicy siatki
        $grid[] = $data;
    }

    // Zamknięcie uchwytu pliku
    fclose($handle);

    // Parametry paginacji
    $perPage = 10; // Liczba wierszy na stronę
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1; // Aktualna strona

    // Wyszukiwanie
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
    if (!empty($searchTerm)) {
        $grid = searchGrid($grid, $searchTerm);
    }

    // Sortowanie
    $sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 0;
    $sortOrder = SORT_ASC;
    $sortedGrid = sortGrid($grid, $sortColumn, $sortOrder);

    // Paginacja
    $totalRows = count($sortedGrid); // Całkowita liczba wierszy
    $totalPages = ceil($totalRows / $perPage); // Całkowita liczba stron

    // Ustalenie początkowego i końcowego indeksu wierszy na podstawie bieżącej strony
    $startIndex = ($currentPage - 1) * $perPage;
    $endIndex = $startIndex + $perPage - 1;
    $endIndex = min($endIndex, $totalRows - 1);

    // Ograniczenie siatki do bieżącej strony
    $pagedGrid = array_slice($sortedGrid, $startIndex, $perPage);

    // Wyświetlanie siatki
    echo '<html>';
    echo '<head>';
    echo '<title>Tytuł strony</title>';
    echo '</head>';
    echo '<body style="background-color: #428bca;">';
    echo '<div style="text-align: center; padding: 20px;">';
    echo '<img src="logo.png" alt="Logo" style="width: 200px; height: 200px;">';
    echo '<h1 style="color: #fff;">Tytuł strony</h1>';
    echo '<form method="get" action="">';
    echo '<label for="search" style="color: #fff;">Wyszukaj: </label>';
    echo '<input type="text" id="search" name="search" value="' . htmlspecialchars($searchTerm, ENT_QUOTES) . '">';
    echo '<input type="submit" value="Szukaj">';
    echo '</form>';
    echo '</div>';

    echo '<div style="margin: 0 auto; width: 800px;">';
    echo '<table border="1">';

    // Wyświetlanie nagłówków
    echo '<tr>';
    foreach ($header as $index => $column) {
        echo '<th style="background-color: #aad5f9; color: #000; padding: 5px;">';
        echo '<a href="?sort=' . $index . '&page=' . $currentPage . '&search=' . htmlspecialchars($searchTerm, ENT_QUOTES) . '">' . htmlspecialchars($column, ENT_QUOTES) . '</a>';
        echo '</th>';
    }
    echo '</tr>';

    // Wyświetlanie danych
    $rowColor = "#cfe3f9";
    foreach ($pagedGrid as $row) {
        echo '<tr>';
        foreach ($row as $index => $value) {
            $cellColor = ($index % 2 == 0) ? "#cfe3f9" : "#aad5f9";
            echo '<td style="background-color: ' . $cellColor . '; padding: 5px; color: #000;">' . htmlspecialchars($value, ENT_QUOTES) . '</td>';
        }
        echo '</tr>';
        $rowColor = ($rowColor == "#cfe3f9") ? "#aad5f9" : "#cfe3f9";
    }

    echo '</table>';
    echo '</div>';

    // Wyświetlanie paginacji
    echo '<div style="margin-top: 10px; text-align: center;">';
    echo 'Strona: ';
    for ($page = 1; $page <= $totalPages; $page++) {
        $activeClass = $page == $currentPage ? 'active' : '';
        echo '<a href="?page=' . $page . '&sort=' . $sortColumn . '&search=' . htmlspecialchars($searchTerm, ENT_QUOTES) . '" style="margin-right: 5px; padding: 5px; background-color: #aad5f9; text-decoration: none; color: #000; border: 1px solid #000; border-radius: 3px;" class="' . $activeClass . '">' . $page . '</a> ';
    }
    echo '</div>';

    echo '</body>';
    echo '</html>';
}

?>
