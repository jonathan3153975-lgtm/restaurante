<section class="hero-panel panel-card card mb-4">
    <div class="card-body p-4 p-xl-5">
        <div class="hero-panel-grid">
            <div>
                <p class="eyebrow">Dashboard operacional</p>
                <h1 class="display-title">Controle salão, caixa e cozinha com visual escuro e acentos dourados.</h1>
                <p class="text-secondary-light mb-4">Este painel simula os módulos prioritários do Tech-Food para validar direção visual, hierarquia e comportamento responsivo.</p>
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newOrderModal">Novo pedido</button>
                    <button class="btn btn-outline-primary">Abrir caixa</button>
                </div>
            </div>
            <div class="highlight-card">
                <p class="eyebrow">Pagamento na mesa</p>
                <h2>Pix com confirmação no caixa</h2>
                <p class="text-secondary-light">Fluxo demonstrativo já preparado para o cliente iniciar o fechamento pela própria mesa.</p>
                <div class="highlight-line"></div>
                <ul class="list-unstyled mb-0 text-secondary-light">
                    <li>QR-Code individual por mesa</li>
                    <li>Atualização em tempo real no painel</li>
                    <li>Conciliação com operador de caixa</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="stats-grid mb-4">
    <?php foreach ($stats as $stat): ?>
        <article class="card panel-card stat-card">
            <div class="card-body">
                <div class="stat-head">
                    <p class="mb-0 text-secondary-light"><?= e($stat['label']) ?></p>
                    <span class="stat-icon"><i class="bi <?= e($stat['icon']) ?>"></i></span>
                </div>
                <h3><?= e($stat['value']) ?></h3>
                <span class="trend-chip"><?= e($stat['trend']) ?></span>
            </div>
        </article>
    <?php endforeach; ?>
</section>

<section class="content-grid mb-4">
    <div class="card panel-card" id="operacao">
        <div class="card-body p-4">
            <div class="section-header">
                <div>
                    <p class="eyebrow">Operação em tempo real</p>
                    <h2 class="section-title">Mesas e pedidos ativos</h2>
                </div>
                <button class="btn btn-outline-primary">Exportar</button>
            </div>

            <form class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label" for="statusFilter">Status</label>
                    <select id="statusFilter" class="form-select">
                        <option>Todos</option>
                        <option>Em preparo</option>
                        <option>Aguardando pagamento</option>
                        <option>Pronta para liberar</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label" for="searchTable">Busca rápida</label>
                    <input id="searchTable" class="form-control" type="text" placeholder="Mesa, pedido ou cliente">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-primary w-100">Aplicar filtro</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Mesa</th>
                            <th>Status</th>
                            <th>Canal</th>
                            <th>Total</th>
                            <th class="text-end">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tables as $table): ?>
                            <tr>
                                <td><?= e($table['table']) ?></td>
                                <td><span class="table-status"><?= e($table['status']) ?></span></td>
                                <td><?= e($table['channel']) ?></td>
                                <td><?= e($table['amount']) ?></td>
                                <td class="text-end"><a href="#" class="table-link">Abrir ficha</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="stack-grid">
        <div class="card panel-card" id="financeiro">
            <div class="card-body p-4">
                <p class="eyebrow">Linha do tempo</p>
                <h2 class="section-title mb-4">Eventos recentes</h2>

                <div class="timeline-list">
                    <?php foreach ($timeline as $item): ?>
                        <article class="timeline-item">
                            <span class="timeline-time"><?= e($item['time']) ?></span>
                            <div>
                                <h3><?= e($item['title']) ?></h3>
                                <p><?= e($item['text']) ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="card panel-card" id="cadastros">
            <div class="card-body p-4">
                <p class="eyebrow">Editor rico</p>
                <h2 class="section-title mb-3">Comunicado do turno</h2>
                <div id="quickNotesEditor" class="quill-shell">
                    <p><strong>Checklist do jantar</strong></p>
                    <p>Confirmar estação de sobremesas, revisar estoque de vinhos e alinhar tempo de saída dos pratos principais.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="newOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <p class="eyebrow mb-1">Fluxo demonstrativo</p>
                    <h2 class="modal-title fs-5">Novo pedido manual</h2>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="mesaModal">Mesa</label>
                        <select id="mesaModal" class="form-select">
                            <option>Mesa 01</option>
                            <option>Mesa 04</option>
                            <option>Mesa 08</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label" for="clienteModal">Cliente / observação</label>
                        <input id="clienteModal" class="form-control" type="text" placeholder="Nome do cliente ou instruções da cozinha">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="itemModal">Item principal</label>
                        <input id="itemModal" class="form-control" type="text" placeholder="Ex.: Risoto de camarão premium">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">Salvar rascunho</button>
            </div>
        </div>
    </div>
</div>