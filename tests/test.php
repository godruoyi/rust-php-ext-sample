<?php

if (class_exists('Parallel\Parallel')) {
    echo "Parallel class exists\n";
} else {
    throw new RuntimeException("Parallel class does not exist");
}

$p = new Parallel\Parallel();
