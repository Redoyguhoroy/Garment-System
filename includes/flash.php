<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function flash_set(string $type, string $msg): void {
  $_SESSION["flash"] = ["type" => $type, "msg" => $msg];
}

function flash_get(): ?array {
  if (!isset($_SESSION["flash"])) return null;
  $f = $_SESSION["flash"];
  unset($_SESSION["flash"]);
  return $f;
}
