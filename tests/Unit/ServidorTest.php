<?php
use Tests\TestCase;

test('nao deve cadastrar servidor com carga horaria negativa', function () {
    $dados = ['carga_horaria' => -20];
    $this->expectException(\Exception::class);
    $servidor = new ServidorFake($dados);
    $servidor->save();
});

class ServidorFake {
    public $carga_horaria;
    public function __construct($dados) {
        $this->carga_horaria = $dados['carga_horaria'] ?? 0;
    }
    public function save() {
        if ($this->carga_horaria <= 0) {
            throw new \Exception("Carga horária inválida!");
        }
        return true;
    }
}
