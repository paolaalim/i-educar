<?php

namespace Tests\Unit\Modules\Avaliacao\Service\Boletim; // Namespace baseado no caminho do arquivo

use PHPUnit\Framework\TestCase;

// Como o projeto é legado (antigo) e não usa namespaces modernos,
// precisamos carregar o Trait e a Classe manualmente.
// O __DIR__ . '/../../../../../.. ' volta 6 níveis de pasta
// (de tests/Unit/Modules/Avaliacao/Service/Boletim para a raiz do projeto)
require_once __DIR__ . '/../../../../../../ieducar/modules/RegraAvaliacao/Model/Regra.php';
require_once __DIR__ . '/../../../../../../ieducar/modules/Avaliacao/Service/Boletim/RegraAvaliacao.php';

/**
 * Classe 'dublê' (stub) apenas para testar o Trait.
 * O Trait não pode ser instanciado diretamente.
 */
class TraitTestClass
{
    // Usa o Trait que queremos testar
    // A barra \ no início indica que o Trait está no namespace "global"
    use \Avaliacao_Service_Boletim_RegraAvaliacao;

    // Construtor para injetar a regra "mockada"
    public function __construct(\RegraAvaliacao_Model_Regra $regra)
    {
        $this->_setRegra($regra);
    }
}

/**
 * Teste para o Trait Avaliacao_Service_Boletim_RegraAvaliacao
 */
class RegraAvaliacaoTest extends TestCase
{
    private $regraMock;

    /**
     * Configura o mock da Regra de Avaliação antes de cada teste
     */
    protected function setUp(): void
    {
        // Criamos um mock da Regra (o objeto que armazena os dados)
        // A barra \ é necessária porque a classe está no namespace global
        $this->regraMock = $this->createMock(\RegraAvaliacao_Model_Regra::class);
    }

    /**
     * CT1: Testa o cenário Verdadeiro (V e V)
     * Cobertura MC/DC: Combinação 1 (VV -> V)
     */
    public function testGetRegraAvaliacaoAprovarPelaFrequenciaAposExame_CT1_RetornaVerdadeiro()
    {
        // Configuração do Mock (Cenário)
        $this->regraMock->method('get')
            ->willReturnMap([
                ['aprovarPelaFrequenciaAposExame', true],  // C1 = V
                ['formulaRecuperacao', 123] // C2 = V (qualquer valor não-nulo)
            ]);

        // Instancia a classe de teste com o mock
        $service = new TraitTestClass($this->regraMock);

        // Execução e Verificação
        $this->assertTrue($service->getRegraAvaliacaoAprovarPelaFrequenciaAposExame());
    }

    /**
     * CT2: Testa o cenário Falso (V e F)
     * Cobertura MC/DC: Combinação 2 (VF -> F)
     */
    public function testGetRegraAvaliacaoAprovarPelaFrequenciaAposExame_CT2_RetornaFalsoSemFormula()
    {
        // Configuração do Mock (Cenário)
        $this->regraMock->method('get')
            ->willReturnMap([
                ['aprovarPelaFrequenciaAposExame', true],  // C1 = V
                ['formulaRecuperacao', null] // C2 = F (valor nulo)
            ]);

        // Instancia a classe de teste com o mock
        $service = new TraitTestClass($this->regraMock);

        // Execução e Verificação
        $this->assertFalse($service->getRegraAvaliacaoAprovarPelaFrequenciaAposExame());
    }

    /**
     * CT3: Testa o cenário Falso (F e V)
     * Cobertura MC/DC: Combinação 3 (FV -> F)
     */
    public function testGetRegraAvaliacaoAprovarPelaFrequenciaAposExame_CT3_RetornaFalsoRegraDesabilitada()
    {
        // Configuração do Mock (Cenário)
        $this->regraMock->method('get')
            ->willReturnMap([
                ['aprovarPelaFrequenciaAposExame', false], // C1 = F
                ['formulaRecuperacao', 123] // C2 = V (qualquer valor não-nulo)
            ]);

        // Instancia a classe de teste com o mock
        $service = new TraitTestClass($this->regraMock);

        // Execução e Verificação
        $this->assertFalse($service->getRegraAvaliacaoAprovarPelaFrequenciaAposExame());
    }
}