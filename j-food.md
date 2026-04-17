Sistema de administração de restaurante.
O nome do sistema será Tech-Food.
Faça o sistema sem framework de estilos e backend. Estrutura MVC, Clean-code;
Conexões e gerenciamento do banco de dados utilizando PDO
Banco de dados MariaDB;
Crie arquivos de criação de database;

Inclua em uma pasta e sempre que houver alterações no banco, gere um novo arquivo migration para complementar o banco;

Crie um layout padrão para todo o sistema com visual moderno, com alteração de visualização diurno e noturno. Faça os cards com linhas retas e utilize box-shadow de maneira elegante.
O sistema deve ser totalmente responsivo
O sistema será desenvolvido em algumas camadas.
Inicialmente terá um acesso administrativo, gerente (que estará incluso em administrativo também) e cliente.

O modo administrativo terá um menu Restaurante com os seguintes submenus.
    1 - Cardápio, mesas, pedidos e caixa (inicialmente);
        - Em cardápio:
            1 - Faça um painel com uma tabela dos itens cadastrados, com opção de desativar, excluir, editar, visualizar. Inclua no topo um campo com filtro dinâmico (digitar e filtrar o conteúdo da tabela), filtro por categoria e um botão para incluir novos itens. Inclua paginação na tabela. O cadastro de itens terá Categoria (retornando as categorias registradas no sistema), imagem, Título, descrição, ingredientes editáveis (Que o usuário poderá solicitar que retire), ingredientes adcionais com preço adcional, preço de custo, preço de venda; As categorias poderão ser cadastradas no ato do registro de item (crie um acesso rápido, abrindo um modal para registrar a categoria, sem fechar o registro de item) exemplo de categorias: Hamburguer, Cervejas, Drinks, etc;
            - A imagem deve ser redimensionável, para poder enquadrar melhor no cardápio;
            - Os campos de valores, inclua as máscaras, acrescentando a vírgula após a segunda casa decimal.
            - Inclua também campos que controlem itens estocáveis (definidos pelo administrador), como refrigerantes, cervejas, etc. Esses itens terão seu controle de estoque feito através da entrada de produtos no módulo estoque e saída através da venda.
            Crie um módulo que acesse a visualização do cardápio, como o usuário verá quando acessar.
        
        - Em mesas:
            Crie um painel semelhante que liste as mesas do restaurante, marcando quantidade de cadeiras. Crie os filtros para verificar mesas ocupadas e livres. Nas mesas desocupadas, inclua botão de editar, desabilitar, excluir e um botão para gerar o QR-code da mesa, para que o garçom possa, ao ocupar a mesa, gerar o QA-code para o cliente fazer o pedido. Em mesas ocupadas, liste também o status do pedido e tempo de pedido em aberto. Crie um formulário para cadastrar novas mesas e gerar um QR-code para disponibilizar na mesa física, para que o cliente, ao ler o QR-Code com o celular, consiga se registrar na mesa (apenas com o nome) e acessar o cardápio para fazer o pedido.
            Quando o cliente se registrar na mesa, marca automaticamente como ocupada nesse painel.

            Faça um módulo também que permita ao garçom fazer o pedido do cliente, para os casos de clientes que não queiram fazer pelo sistema.

        - Pedidos.:
            Listar todos os pedidos registrados, priorizando por em aberto e tempo de espera. Inclua filtros como bebidas e refeições, para que a equipe possa diferenciar no ato de servir. Na lista, crie um botão para marcar como entregue;
        
        - caixa:
            Criar um painel de administração da movimentação do restaurante. Constando mesas encerradas, com valor e método de pagamento. Mesas em aberto para fazer o encerramento direto no caixa ou permitindo que o cliente feche diretamente pelo sistema.
O modo cliente terá o seguinte:
    1 - Cardápio e sistema de pedidos (cliente);
        Crie o módulo completo do acesso do cliente, da seguinte forma:
            Mesa desocupada - Leitura de QR-Code para iniciar. Cadastrar nome (faça com sweetAlert). Após isso, libera o cardápio estilo i-food para o cliente selecionar os produtos, marcando itens para retirar ou acrescentar (nos que tenham essa opção), uma página de verificação do pedido antes de confirmar e a confirmação para enviar para pedidos.

            Nos casos de bebidas, marcar se a entrega é imediata ou juntamente com o restante do pedido.

            Após a confirmação, libera para o usuário acompanhar o subtotal de sua fatura e a possibilidade de fazer pedidos adcionais;
Faça as melhorias que julgar útil para um sistema nesse sentido, com base em sistemas desenvolvidos atualmente.

Implemente métodos de pagamentos usuais como pix, cartão de crédito, etc.


    

