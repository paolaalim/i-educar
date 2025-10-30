<?php

namespace Tests\Unit\Modules\Avaliacao\Service\Boletim; // Namespace baseado no caminho do arquivo

use PHPUnit\Framework\TestCase;

// Carrega as classes e o Trait manualmente
require_once __DIR__ . '/../../../../../../ieducar/modules/RegraAvaliacao/Model/Regra.php';
require_once __DIR__ . '/../../../../../../ieducar/modules/Avaliacao/Service/Boletim/RegraAvaliacao.php';

/**
 * Classe 'dublê' (stub) apenas para testar o Trait.
 * O Trait não pode ser instanciado diretamente.
 */
class TraitTestClass
{
    // Usa o Trait que queremos testar
    use \Avaliacao_Service_Boletim_RegraAvaliacao;

    // Construtor para injetar a regra "mockada"
    public function __construct(\RegraAvaliacao_Model_Regra $regra)
    {
        $this->_setRegra($regra);
    }

    // --- Métodos "setter" adicionados para ajudar nos testes ---

    // Este método é necessário para o teste CT4
    public function setCodigoDisciplinasAglutinadas($codigos)
    {
        $this->_codigoDisciplinasAglutinadas = $codigos;
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
        // Criamos um mock da Regra
        $this->regraMock = $this->createMock(\RegraAvaliacao_Model_Regra::class);

        // --- Configuração para os testes CT5 e CT6 ---
        // Precisamos que a propriedade 'disciplinasAglutinadas' exista no Mock
        // para que a função "empty()" e "explode()" funcionem.
        // Adicionamos esta propriedade ao mock.
        if (!property_exists($this->regraMock, 'disciplinasAglutinadas')) {
            $this->regraMock->disciplinasAglutinadas = null;
        }
    }

    /*****************************************************************
     * TESTES (CT1, CT2, CT3) - MÉTODO 1: getRegraAvaliacaoAprovarPelaFrequenciaAposExame()
     * (Critério: Duas condições em uma decisão - MC/DC)
     *****************************************************************/

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


    /*****************************************************************
     * TESTES (CT4, CT5, CT6) - MÉTODO 2: codigoDisciplinasAglutinadas()
     * (Critério: Pelo menos duas decisões)
     *****************************************************************/

    /**
     * CT4: Teste para Decisão 1 (linha 17) = FALSO
     * (Já foi setado, deve retornar o valor direto)
     */
    public function testCodigoDisciplinasAglutinadas_CT4_JaSetado()
    {
        // Instancia a classe com o mock
        $service = new TraitTestClass($this->regraMock);
        
        // Seta um valor manualmente (simulando uma primeira chamada)
        $service->setCodigoDisciplinasAglutinadas(['99']);

        // Executa (Decisão 1 na linha 17 será FALSA)
        $resultado = $service->codigoDisciplinasAglutinadas();

        $this->assertEquals(['99'], $resultado);
    }

    /**
     * CT5: Teste para Decisão 1 (linha 17) = VERDADEIRO
     * Decisão 2 (linha 18) = VERDADEIRO
     * (Não foi setado, e a regra está vazia)
     */
    public function testCodigoDisciplinasAglutinadas_CT5_NaoSetado_RegraVazia()
    {
        // Configuração do Mock
        $this->regraMock->disciplinasAglutinadas = ''; // Faz 'empty()' (Decisão 2) ser VERDADEIRO
        $service = new TraitTestClass($this->regraMock);

        // Executa (Decisão 1 = V, Decisão 2 = V)
        $resultado = $service->codigoDisciplinasAglutinadas();

        // Verifica se retorna um array vazio
        $this->assertEquals([], $resultado);
    }

    /**
     * CT6: Teste para Decisão 1 (linha 17) = VERDADEIRO
     * Decisão 2 (linha 18) = FALSO
     * (Não foi setado, e a regra tem valores)
     */
    public function testCodigoDisciplinasAglutinadas_CT6_NaoSetado_RegraComValores()
    {
        // Configuração do Mock
        $this->regraMock->disciplinasAglutinadas = '10,20,30'; // Faz 'empty()' (Decisão 2) ser FALSO
        $service = new TraitTestClass($this->regraMock);

        // Executa (Decisão 1 = V, Decisão 2 = F)
        $resultado = $service->codigoDisciplinasAglutinadas();

        // Verifica se ele separou a string corretamente
        $this->assertEquals(['10', '20', '30'], $resultado);
    }
}
