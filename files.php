<?php

$stmt = $pdo->prepare('SELECT * FROM releases WHERE file=?');
$stmt->execute([$__pages[1]]);
$row = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

if (!$row['file']) {
	header('Location: /');
} else {
	$stmt = $pdo->prepare('UPDATE releases SET downloads=? WHERE file=?');
	$stmt->execute([$row['downloads']+1, $__pages[1]]);

	header('Location: '.$row['mirror'].$row['file']);
}
