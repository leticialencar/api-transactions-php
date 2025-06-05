<?php

namespace Src\Model;

class Transacao {
    private $id;
    private $valor;
    private $dataHora;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getValor() {
        return $this->valor;
    }

    public function setValor($valor) {
        $this->valor = $valor;
    }

    public function getDataHora() {
        return $this->dataHora;
    }

    public function setDataHora($dataHora) {
        $this->dataHora = $dataHora;
    }
}