<?php
/**
 * Sillage - Script de População Inicial (Seed) do Banco de Dados
 * Desenvolvido por Kauã Freitas e Yasmin Cristiny
 */

require_once __DIR__ . '/../config/db.php';

echo "Inicializando banco de dados...\n";
initializeDatabase();
$pdo = getDBConnection();

// 1. Criar Administrador Inicial se não existir
$checkAdm = $pdo->prepare("SELECT id FROM usuarios_adm WHERE email = :email");
$checkAdm->execute([':email' => 'admin@sillage.com']);
$adm = $checkAdm->fetch();

if (!$adm) {
    $hashedPassword = password_hash('admin123', PASSWORD_BCRYPT);
    $stmtAdm = $pdo->prepare("INSERT INTO usuarios_adm (nome, email, senha, data_cadastro) VALUES (:nome, :email, :senha, NOW())");
    $stmtAdm->execute([
        ':nome'  => 'Kauã Freitas & Yasmin Cristiny',
        ':email' => 'admin@sillage.com',
        ':senha' => $hashedPassword
    ]);
    $adminId = (int)$pdo->lastInsertId();
    echo "Administrador criado com sucesso (ID: $adminId, email: admin@sillage.com, senha: admin123)\n";
} else {
    $adminId = (int)$adm['id'];
    echo "Administrador existente encontrado (ID: $adminId)\n";
}

// 2. Preparar diretório de imagens e baixar/gerar amostras
$samplesDir = __DIR__ . '/../assets/img/samples/';
if (!is_dir($samplesDir)) {
    mkdir($samplesDir, 0777, true);
}

// Criar imagens de amostra SVG estilizadas e elegantes para cada perfume
$perfumesSvg = [
    'una_somos.svg' => [
        'bg' => '#8B2635',
        'accent' => '#C8A2C8',
        'title' => 'Una Somos',
        'brand' => 'NATURA',
        'shape' => 'round'
    ],
    'fame_robot.svg' => [
        'bg' => '#D4AF37',
        'accent' => '#F2DCB1',
        'title' => 'FAME',
        'brand' => 'PACO RABANNE',
        'shape' => 'robot'
    ],
    'libre_ysl.svg' => [
        'bg' => '#3D4849',
        'accent' => '#F2DCB1',
        'title' => 'Libre Platine',
        'brand' => 'YVES SAINT LAURENT',
        'shape' => 'classic'
    ],
    'sauvage.svg' => [
        'bg' => '#1C2D42',
        'accent' => '#9FAF90',
        'title' => 'Sauvage Elixir',
        'brand' => 'DIOR',
        'shape' => 'cylinder'
    ],
    'baccarat.svg' => [
        'bg' => '#7B1113',
        'accent' => '#F2DCB1',
        'title' => 'Baccarat Rouge 540',
        'brand' => 'MAISON FRANCIS KURKDJIAN',
        'shape' => 'crystal'
    ],
    'curiosidade_piramide.svg' => [
        'bg' => '#5C6B5E',
        'accent' => '#C8A2C8',
        'title' => 'Pirâmide Olfativa',
        'brand' => 'SILLAGE GUIA',
        'shape' => 'triangle'
    ]
];

foreach ($perfumesSvg as $filename => $info) {
    $filePath = $samplesDir . $filename;
    if (!file_exists($filePath)) {
        $svgContent = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600" width="600" height="600">
  <defs>
    <radialGradient id="grad-{$filename}" cx="50%" cy="40%" r="60%">
      <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.25"/>
      <stop offset="100%" stop-color="#000000" stop-opacity="0.4"/>
    </radialGradient>
    <filter id="shadow" x="-20%" y="-20%" width="140%" height="140%">
      <feDropShadow dx="0" dy="16" stdDeviation="18" flood-color="#000" flood-opacity="0.28"/>
    </filter>
  </defs>
  <rect width="600" height="600" fill="#EAE5D9"/>
  <!-- Suporte e base -->
  <ellipse cx="300" cy="510" rx="190" ry="24" fill="#9FAF90" opacity="0.3"/>
  <ellipse cx="300" cy="505" rx="140" ry="16" fill="#5C6B5E" opacity="0.25"/>
  
  <!-- Frasco de Perfume Estilizado -->
  <g filter="url(#shadow)">
    <!-- Tampa / Borrifador -->
    <rect x="272" y="140" width="56" height="40" rx="4" fill="{$info['accent']}" stroke="#5C6B5E" stroke-width="2"/>
    <rect x="286" y="115" width="28" height="25" rx="3" fill="#D4AF37"/>
    <ellipse cx="300" cy="115" rx="14" ry="4" fill="#F2DCB1"/>
    
    <!-- Ombro do frasco -->
    <path d="M 240 210 L 272 180 L 328 180 L 360 210 Z" fill="{$info['bg']}"/>
    
    <!-- Corpo Principal do Frasco -->
    <rect x="220" y="210" width="160" height="260" rx="20" fill="{$info['bg']}"/>
    <rect x="220" y="210" width="160" height="260" rx="20" fill="url(#grad-{$filename})"/>
    
    <!-- Rótulo Dourado / Champagne -->
    <rect x="245" y="270" width="110" height="130" rx="6" fill="#F2DCB1" stroke="{$info['accent']}" stroke-width="1.5"/>
    <text x="300" y="305" font-family="'Cinzel', 'Playfair Display', serif" font-size="11" font-weight="bold" fill="#5C6B5E" text-anchor="middle" letter-spacing="2">{$info['brand']}</text>
    <line x1="260" y1="318" x2="340" y2="318" stroke="#9FAF90" stroke-width="1"/>
    <text x="300" y="342" font-family="'Playfair Display', serif" font-size="14" font-weight="bold" fill="#3D4849" text-anchor="middle">{$info['title']}</text>
    <text x="300" y="365" font-family="'Plus Jakarta Sans', sans-serif" font-size="9" fill="#5C6B5E" text-anchor="middle" letter-spacing="1">EAU DE PARFUM</text>
    <text x="300" y="385" font-family="'Plus Jakarta Sans', sans-serif" font-size="8" fill="#7A8B7C" text-anchor="middle">SILLAGE COLLECTION</text>
  </g>
  
  <!-- Aura de fragrância (Sillage) -->
  <path d="M 120 180 Q 180 120 280 150 Q 380 180 470 120" stroke="{$info['accent']}" stroke-width="2.5" fill="none" opacity="0.6" stroke-dasharray="6,4"/>
  <path d="M 140 220 Q 210 160 300 200 Q 390 240 460 180" stroke="#F2DCB1" stroke-width="2" fill="none" opacity="0.5"/>
</svg>
SVG;
        file_put_contents($filePath, $svgContent);
    }
}

// 3. Cadastrar Posts Iniciais se a tabela estiver vazia
$postsCount = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();

if ($postsCount == 0) {
    echo "Cadastrando postagens de amostra...\n";

    $seedPosts = [
        // LANÇAMENTOS (noticias)
        [
            'titulo' => 'Natura Una Somos: A Potência do Floral Amadeirado Brasileiro',
            'nome_perfume' => 'Una Somos',
            'concentracao' => 'Deo Parfum (20% essência)',
            'familia_olfativa' => 'Floral Amadeirado Intenso',
            'notas_topo' => 'Pimenta Rosa, Bergamota Fresca e Mandarina Silvestre',
            'notas_corpo' => 'Jasmim Sambac, Íris Aveludada e Rosa de Damasco',
            'notas_fundo' => 'Patchouli Vivo, Breu Branco, Âmbar e Baunilha de Madagascar',
            'clima' => 'Outono/Inverno, noites elegantes e temperaturas amenas',
            'categoria' => 'noticias',
            'imagem' => 'assets/img/samples/una_somos.svg',
            'conteudo' => '<p>O mercado nacional de alta perfumaria acaba de ganhar um novo capítulo de destaque com a chegada de <strong>Una Somos</strong>, a mais nova joia da Casa de Perfumaria do Brasil. Desenvolvido para celebrar a multiplicidade e a potência feminina, o perfume combina a exuberância de florais brancos sofisticados com a ancestralidade das resinas da biodiversidade brasileira.</p>
            <p>Com assinatura olfativa de Verônica Kato em colaboração com perfumistas de prestígio internacional, a fragrância traz o toque inconfundível do <em>Breu Branco</em>, resina colhida de forma sustentável no coração da floresta amazônica, conferindo uma assinatura amadeirada cremosa e profundamente envolvente.</p>
            <h3>A Experiência Olfativa</h3>
            <p>Desde a primeira borrifada, sente-se uma explosão picante e cítrica conduzida pela pimenta rosa e notas suculentas de bergamota. Rapidamente, a composição desabrocha para um coração floral nobre com pétalas aveludadas de íris e jasmim sambac. O grande diferencial reside no rastro (o legítimo <em>sillage</em>), onde notas quentes de baunilha, âmbar e o patchouli vivo garantem fixação memorável na pele de mais de 10 horas.</p>',
            'referencias' => 'Casa de Perfumaria do Brasil / Natura Cosméticos (2024); Fragrantica Brasil (Ficha Técnica Una Somos).'
        ],
        [
            'titulo' => 'Paco Rabanne Fame: A Revolução Tropical e Gourmand em Paris',
            'nome_perfume' => 'Fame Eau de Parfum',
            'concentracao' => 'Eau de Parfum',
            'familia_olfativa' => 'Floral Frutado Amadeirado',
            'notas_topo' => 'Manga Suculenta da Índia e Bergamota Radiante',
            'notas_corpo' => 'Jasmim Puro de Grasse e Notas de Flor de Laranjeira',
            'notas_fundo' => 'Incenso Cremoso, Sândalo Aveludado e Baunilha Bourbon',
            'clima' => 'Primavera/Verão, encontros diurnos e noites descontraídas',
            'categoria' => 'noticias',
            'imagem' => 'assets/img/samples/fame_robot.svg',
            'conteudo' => '<p>Com seu frasco ultra vanguardista que replica um vestido de cota de malha metálica cravejado de dourado e óculos escuros, <strong>Fame</strong> da Maison Paco Rabanne consolida-se como um dos maiores lançamentos contemporâneos.</p>
            <p>A composição aposta na união inusitada entre a doçura solar da manga fresca com a solenidade mística do incenso somali, equilibrados pela pureza do jasmim cultivado em Grasse através de extração por micro-líquidos.</p>
            <h3>Por que se tornou um ícone?</h3>
            <p>Fame foge dos florais convencionais ao entregar uma vibração ensolarada, alegre e luxuosa. A doçura da manga não é infantil; ela se funde ao calor do sândalo e à cremosidade da baunilha bourbon, gerando uma silagem convidativa e marcante.</p>',
            'referencias' => 'Puig Fragrances / Paco Rabanne Press Release; Revista Vogue Paris (Edição Especial Lançamentos).'
        ],
        [
            'titulo' => 'YSL Libre L\'Absolu Platine: O Frio e o Calor em Harmonia Nobre',
            'nome_perfume' => 'Libre L\'Absolu Platine',
            'concentracao' => 'Extrait de Parfum',
            'familia_olfativa' => 'Floral Âmbar Fougère',
            'notas_topo' => 'Aldeídos Brancos, Bergamota da Calábria e Mandarina',
            'notas_corpo' => 'Flor de Laranjeira do Marrocos e Lavanda Branca Diva',
            'notas_fundo' => 'Baunilha Bourbon de Madagascar e Âmbar Gris Mineral',
            'clima' => 'Versátil — do ambiente climatizado a eventos noturnos sofisticados',
            'categoria' => 'noticias',
            'imagem' => 'assets/img/samples/libre_ysl.svg',
            'conteudo' => '<p>A saga <strong>Libre</strong> de Yves Saint Laurent atinge seu ápice de intensidade com <em>L\'Absolu Platine</em>. Pela primeira vez na linha, a tradicional tensão entre a flor de laranjeira ardente do Marrocos e a lavanda francesa é resfriada por um acorde exclusivo de lavanda branca metálica.</p>
            <p>Essa dualidade cria uma sensação sensorial única na pele: o início gelado e crocante dos aldeídos funde-se ao calor sensual das resinas e da baunilha bourbon, oferecendo uma assinatura de poder, autoridade e liberdade.</p>',
            'referencias' => 'L\'Oréal Luxe Division; YSL Beauty Press.'
        ],

        // RESENHAS
        [
            'titulo' => 'Resenha: Natura Una Somos na Pele — Fixação, Projeção e Desempenho Real',
            'nome_perfume' => 'Natura Una Somos',
            'concentracao' => 'Deo Parfum (20% concentração de óleos)',
            'familia_olfativa' => 'Floral Amadeirado Nobre',
            'notas_topo' => 'Pimenta Rosa, Mandarina e Acorde Cítrico Floral',
            'notas_corpo' => 'Íris Florentina, Rosa Damascena e Jasmim Sambac',
            'notas_fundo' => 'Breu Branco Amazônico, Patchouli e Baunilha',
            'clima' => 'Outono, Inverno e eventos noturnos elegantes',
            'categoria' => 'resenhas',
            'imagem' => 'assets/img/samples/una_somos.svg',
            'conteudo' => '<p>Testamos exaustivamente o <strong>Una Somos</strong> ao longo de três semanas sob diferentes variações de temperatura e ambientes fechados. O veredito é inequívoco: a Natura entregou uma das melhores construções amadeiradas da sua história.</p>
            <h3>Abertura e Evolução</h3>
            <p>A saída é vivaz, sem a pungência excessiva de álcool. A pimenta rosa equilibra-se perfeitamente com a bergamota, gerando um frescor cintilante nos primeiros 20 minutos. Conforme a secagem (<em>drydown</em>) progride, a íris confere uma faceta atalcada de extremo bom gosto, que remete aos grandes clássicos europeus, mas com o calor brasileiro do breu branco.</p>
            <h3>Projeção e Fixação</h3>
            <ul>
                <li><strong>Projeção:</strong> 2 horas e meia a uma braçada de distância de forma constante e educada.</li>
                <li><strong>Fixação:</strong> Entre 9 a 11 horas na pele; em tecidos, dura até a lavagem.</li>
                <li><strong>Silagem (Sillage):</strong> Deixa um rastro envolvente e sedutor no ambiente, perfeito para jantares e ocasiões especiais.</li>
            </ul>
            <p><strong>Nota Sillage:</strong> 9.4 / 10.</p>',
            'referencias' => 'Análise sensorial da equipe Sillage; Lote de testes oficial Natura Cosméticos.'
        ],
        [
            'titulo' => 'Resenha: Paco Rabanne Fame — Vale o Hype além do Frasco de Ouro?',
            'nome_perfume' => 'Paco Rabanne Fame',
            'concentracao' => 'Eau de Parfum',
            'familia_olfativa' => 'Floral Frutado Quente',
            'notas_topo' => 'Manga Tropical e Bergamota',
            'notas_corpo' => 'Jasmim Solar e Flor de Laranjeira',
            'notas_fundo' => 'Incenso Místico e Baunilha Aveludada',
            'clima' => 'Primavera e Verão, fins de tarde e passeios ao ar livre',
            'categoria' => 'resenhas',
            'imagem' => 'assets/img/samples/fame_robot.svg',
            'conteudo' => '<p>Muitos críticos desconfiavam se o frasco extravagante de robô dourado mascarava uma fragrância genérica. Após nossos testes minuciosos, comprovamos que a união da manga com incenso produz uma alquimia inesperadamente viciante.</p>
            <p>O incenso quebra qualquer sensação excessivamente doce ou infantil da fruta, atribuindo textura aveludada e misteriosa. É um perfume solar, jovial, mas com postura refinada.</p>
            <h3>Desempenho</h3>
            <p>Fixou por 7 horas e meia em pele hidratada, com silagem moderada e agradável. Excelente para quem busca uma assinatura olfativa luminosa e descontraída.</p>',
            'referencias' => 'Testes sensoriais independentes — Grupo Sillage; Puig International.'
        ],
        [
            'titulo' => 'Resenha: Dior Sauvage Elixir — O Monstro Especiado de François Demachy',
            'nome_perfume' => 'Sauvage Elixir',
            'concentracao' => 'Elixir / Parfum Concentré',
            'familia_olfativa' => 'Aromático Especiado Amadeirado',
            'notas_topo' => 'Canela, Noz-moscada, Cardamomo e Toranja Amarga',
            'notas_corpo' => 'Lavanda Francesa de Nyons',
            'notas_fundo' => 'Alcaçuz, Sândalo de Sri Lanka, Âmbar e Patchouli',
            'clima' => 'Frio intenso, noites de inverno e ambientes abertos',
            'categoria' => 'resenhas',
            'imagem' => 'assets/img/samples/sauvage.svg',
            'conteudo' => '<p>Sauvage Elixir não é apenas um flanker; é uma reinterpretação quase madura e sombria do DNA original de Sauvage. Aqui, o ambroxan dá lugar a um banquete de especiarias nobres e raras.</p>
            <p>A noz-moscada e o cardamomo explodem com calor e picância, amparados por uma lavanda límpida e pura cultivada sob medida para a Dior. A base de alcaçuz e sândalo cria uma densidade hipnótica.</p>
            <h3>Fixação e Potência</h3>
            <p>Cuidado com as borrifadas! Duas a três borrifadas são suficientes para mais de 14 horas de fixação e uma projeção atômica nas primeiras 4 horas.</p>',
            'referencias' => 'Parfums Christian Dior; Olfactive Studio Report.'
        ],

        // CURIOSIDADES / INFORMAÇÕES (Sidebar topics and articles)
        [
            'titulo' => 'O que é Pirâmide Olfativa e Como Decifrar Suas Notas?',
            'nome_perfume' => 'Guia Didático Sillage',
            'concentracao' => 'Conceito Teórico',
            'familia_olfativa' => 'Perfumaria Geral',
            'notas_topo' => 'Notas de Saída / Topo: Voláteis, duram de 5 a 15 minutos',
            'notas_corpo' => 'Notas de Coração / Corpo: Personalidade, duram de 2 a 4 horas',
            'notas_fundo' => 'Notas de Base / Fundo: Fixadores pesados, duram 6 a 24 horas',
            'clima' => 'Aplicável a todas as estações e criações',
            'categoria' => 'curiosidades',
            'imagem' => 'assets/img/samples/curiosidade_piramide.svg',
            'conteudo' => '<p>A pirâmide olfativa é a representação gráfica de como uma fragrância se comporta quimicamente ao longo do tempo após a aplicação na pele. Como as moléculas possuem pesos moleculares e taxas de evaporação diferentes, o perfume se transforma em etapas harmoniosas.</p>
            <h3>1. Notas de Topo (Saída)</h3>
            <p>São as primeiras moléculas que atingem o olfato ao borrifar. Possuem peso molecular leve e alta volatilidade. Geralmente compostas por cítricos (limão, bergamota), ervas frescas (menta, alecrim) e frutas aquosas. Duram entre 5 e 15 minutos.</p>
            <h3>2. Notas de Coração (Corpo)</h3>
            <p>Representam a alma e o tema central do perfume. Começam a se revelar quando o topo se dissipa. Florais nobres (rosa, jasmim, néroli), especiarias suaves e notas frutadas densas dominam essa fase, permanecendo de 2 a 4 horas.</p>
            <h3>3. Notas de Fundo (Base)</h3>
            <p>As notas mais densas e pesadas da composição. Atuam como âncoras e fixadores na derme. Madeiras (sândalo, cedro, vetiver), resinas, almíscares, baunilha e âmbar compõem o fundo e podem perdurar por mais de 12 a 24 horas.</p>',
            'referencias' => 'Jean-Claude Ellena, "O Perfume: O Guia do Perfumista"; International Fragrance Association (IFRA).'
        ],
        [
            'titulo' => 'História do Perfume: Das Oferendas no Antigo Egito aos Ateliês de Paris',
            'nome_perfume' => 'Crônica Histórica',
            'concentracao' => 'Artigo Educativo',
            'familia_olfativa' => 'História Cultural',
            'notas_topo' => 'Mirra e Olíbano Sagrados',
            'notas_corpo' => 'Lótus Azul do Rio Nilo',
            'notas_fundo' => 'Cedro do Líbano e Resinas',
            'clima' => 'Universal',
            'categoria' => 'curiosidades',
            'imagem' => 'assets/img/samples/curiosidade_piramide.svg',
            'conteudo' => '<p>A palavra <em>perfume</em> tem origem no latim <em>per fumum</em>, que significa literalmente "através da fumaça". No Antigo Egito, resinas preciosas e madeiras aromáticas eram queimadas em rituais religiosos para elevar preces aos deuses e guiar as almas dos faraós.</p>
            <p>Com o avanço da alquimia árabe no século IX e o aprimoramento da destilação alcoólica por Avicena, os aromas deixaram de ser apenas bálsamos e óleos pesados e ganharam a leveza das águas aromáticas. Já no século XVII, a cidade francesa de Grasse emergiu como a capital mundial do perfume graças à união da indústria do couro perfumado com o cultivo de jasmim, rosas e lavanda.</p>',
            'referencias' => 'Mandy Aftel, "Essência e Alquimia: A História Natural do Perfume"; Museu Internacional da Perfumaria de Grasse.'
        ],
        [
            'titulo' => 'Concentrações: Eau de Cologne, EDT, EDP ou Extrait de Parfum?',
            'nome_perfume' => 'Guia de Concentração',
            'concentracao' => 'Tabela Comparativa',
            'familia_olfativa' => 'Química e Formulação',
            'notas_topo' => 'Splash / EDC: 2% a 5%',
            'notas_corpo' => 'EDT: 8% a 15% | EDP: 15% a 20%',
            'notas_fundo' => 'Parfum / Extrait: 20% a 40%',
            'clima' => 'Todas as ocasiões',
            'categoria' => 'curiosidades',
            'imagem' => 'assets/img/samples/curiosidade_piramide.svg',
            'conteudo' => '<p>A principal diferença entre as denominações de perfumes não é a qualidade dos ingredientes, mas a proporção percentual de óleo essencial diluído em álcool e água desmineralizada:</p>
            <ul>
                <li><strong>Eau de Cologne (EDC):</strong> 2% a 5% de essência. Duração de 2 a 3 horas. Ideal para pós-banho e dias quentes.</li>
                <li><strong>Eau de Toilette (EDT):</strong> 8% a 15% de essência. Duração média de 5 a 7 horas. O formato mais equilibrado para o dia a dia.</li>
                <li><strong>Eau de Parfum (EDP):</strong> 15% a 20% de essência. Duração de 8 a 12 horas. Presença marcante com maior densidade de notas de coração e fundo.</li>
                <li><strong>Parfum / Extrait de Parfum:</strong> 20% a 40% de essência pura. Duração de 12 a 24 horas. Textura mais oleosa e silagem íntima porém persistente.</li>
            </ul>',
            'referencias' => 'Société Française des Parfumeurs; Edmond Roudnitska, "L\'Esthétique de l\'Odorat".'
        ],
        [
            'titulo' => 'Mitos e Verdades: Esfregar os Pulsos Realmente Quebra as Moléculas?',
            'nome_perfume' => 'Dicas de Aplicação',
            'concentracao' => 'Educação Olfativa',
            'familia_olfativa' => 'Técnica Prática',
            'notas_topo' => 'Aquecimento por atrito mecânico',
            'notas_corpo' => 'Alteração do tempo de evaporação',
            'notas_fundo' => 'Preservação da silagem natural',
            'clima' => 'Diário',
            'categoria' => 'curiosidades',
            'imagem' => 'assets/img/samples/curiosidade_piramide.svg',
            'conteudo' => '<p>Quem nunca viu alguém borrifar perfume no pulso e esfregá-lo contra o outro? Mas será que essa prática popular prejudica a fragrância?</p>
            <p><strong>A resposta científica:</strong> Esfregar os pulsos não "quebra" quimicamente as moléculas, pois as ligações covalentes são muito mais fortes do que a força mecânica de um atrito suave. <strong>No entanto</strong>, a fricção gera calor imediato na pele e acelera bruscamente a evaporação das delicadas notas de topo (como cítricos e florais frescos). Como consequência, você perde os primeiros 15 a 30 minutos de evolução desenhados pelo perfumista!</p>
            <p><em>Dica de ouro Sillage:</em> Apenas borrife e deixe o líquido secar naturalmente ao ar.</p>',
            'referencias' => 'Givaudan Fragrance Academy; Osmothèque de Versailles.'
        ],
        [
            'titulo' => 'Como Armazenar Seus Perfumes para Durarem Anos sem Estragar',
            'nome_perfume' => 'Conservação & Cuidado',
            'concentracao' => 'Guia de Preservação',
            'familia_olfativa' => 'Cuidado Pessoal',
            'notas_topo' => 'Inimigo 1: Luz solar e radiação UV',
            'notas_corpo' => 'Inimigo 2: Variação de temperatura e calor',
            'notas_fundo' => 'Inimigo 3: Umidade excessiva (banheiro)',
            'clima' => 'Armazenamento em local seco e escuro',
            'categoria' => 'curiosidades',
            'imagem' => 'assets/img/samples/curiosidade_piramide.svg',
            'conteudo' => '<p>Um frasco de perfume bem guardado pode durar décadas com suas notas originais intactas. Para preservar a sua coleção, mantenha três princípios simples:</p>
            <ol>
                <li><strong>Fuja do banheiro:</strong> O vapor dos chuveiros e as constantes trocas bruscas de temperatura degradam os óleos essenciais e oxidam as notas florais.</li>
                <li><strong>Proteja da luz direta:</strong> A radiação ultravioleta desestabiliza a cor e quebra os componentes aromáticos. Guarde seu perfume na caixa original ou dentro de um armário escuro.</li>
                <li><strong>Mantenha sempre tampado:</strong> O contato contínuo com o ar acelera a oxidação do álcool e a evaporação da fragrância.</li>
            </ol>',
            'referencias' => 'International Journal of Cosmetic Science; Arquivos Sillage de Preservação Olfativa.'
        ]
    ];

    $stmtInsert = $pdo->prepare("INSERT INTO posts (
        titulo, nome_perfume, concentracao, familia_olfativa, 
        notas_topo, notas_corpo, notas_fundo, clima, 
        categoria, imagem, conteudo, referencias, autor_id, data_criacao
    ) VALUES (
        :titulo, :nome_perfume, :concentracao, :familia_olfativa,
        :notas_topo, :notas_corpo, :notas_fundo, :clima,
        :categoria, :imagem, :conteudo, :referencias, :autor_id, NOW()
    )");

    foreach ($seedPosts as $p) {
        $stmtInsert->execute([
            ':titulo'           => $p['titulo'],
            ':nome_perfume'     => $p['nome_perfume'],
            ':concentracao'     => $p['concentracao'],
            ':familia_olfativa' => $p['familia_olfativa'],
            ':notas_topo'       => $p['notas_topo'],
            ':notas_corpo'      => $p['notas_corpo'],
            ':notas_fundo'      => $p['notas_fundo'],
            ':clima'            => $p['clima'],
            ':categoria'        => $p['categoria'],
            ':imagem'           => $p['imagem'],
            ':conteudo'         => $p['conteudo'],
            ':referencias'      => $p['referencias'],
            ':autor_id'         => $adminId
        ]);
        echo "Post inserido: {$p['titulo']}\n";
    }
} else {
    echo "Tabela posts já contém $postsCount registros.\n";
}

echo "Inicialização concluída com sucesso!\n";
