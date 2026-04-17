const TechFood = (() => {
    const storageKey = 'tech-food-theme';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const loadedScripts = new Map();
    const loadedStyles = new Set();

    const clamp = (value, min, max) => Math.min(Math.max(value, min), max);

    const formatMoney = (value) => `R$ ${Number(value || 0).toFixed(2).replace('.', ',')}`;

    const applyMoneyMask = (input) => {
        if (!input || input.dataset.moneyMaskBound === '1') {
            return;
        }

        input.dataset.moneyMaskBound = '1';
        input.addEventListener('input', () => {
            const digits = input.value.replace(/\D/g, '');
            const normalized = (Number(digits || '0') / 100).toFixed(2);
            input.value = normalized.replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        });
    };

    const loadScript = (src) => {
        if (loadedScripts.has(src)) {
            return loadedScripts.get(src);
        }

        const promise = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.defer = true;
            script.onload = resolve;
            script.onerror = reject;
            document.head.append(script);
        });

        loadedScripts.set(src, promise);

        return promise;
    };

    const loadStyle = (href) => {
        if (loadedStyles.has(href)) {
            return;
        }

        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        document.head.append(link);
        loadedStyles.add(href);
    };

    const moneyMasks = () => {
        document.querySelectorAll('.money-mask').forEach(applyMoneyMask);
    };

    const themeToggle = () => {
        const currentTheme = localStorage.getItem(storageKey) ?? 'light';
        document.documentElement.dataset.theme = currentTheme;

        document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
            button.textContent = currentTheme === 'dark' ? 'Modo diurno' : 'Modo noturno';
            button.addEventListener('click', () => {
                const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
                document.documentElement.dataset.theme = nextTheme;
                localStorage.setItem(storageKey, nextTheme);
                document.querySelectorAll('[data-theme-toggle]').forEach((item) => {
                    item.textContent = nextTheme === 'dark' ? 'Modo diurno' : 'Modo noturno';
                });
            });
        });
    };

    const mobileSidebar = () => {
        const shell = document.querySelector('.shell');
        const sidebar = document.querySelector('[data-app-sidebar]');
        const toggleButtons = document.querySelectorAll('[data-sidebar-toggle]');
        const closeButtons = document.querySelectorAll('[data-sidebar-close], [data-sidebar-backdrop]');

        if (!shell || !sidebar || toggleButtons.length === 0) {
            return;
        }

        const syncExpanded = (expanded) => {
            toggleButtons.forEach((button) => {
                button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            });
        };

        const closeSidebar = () => {
            shell.classList.remove('sidebar-open');
            document.body.style.overflow = '';
            syncExpanded(false);
        };

        const openSidebar = () => {
            if (window.innerWidth > 1100) {
                return;
            }

            shell.classList.add('sidebar-open');
            document.body.style.overflow = 'hidden';
            syncExpanded(true);
        };

        toggleButtons.forEach((button) => {
            button.addEventListener('click', () => {
                if (shell.classList.contains('sidebar-open')) {
                    closeSidebar();
                    return;
                }

                openSidebar();
            });
        });

        closeButtons.forEach((button) => {
            button.addEventListener('click', closeSidebar);
        });

        sidebar.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 1100) {
                    closeSidebar();
                }
            });
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 1100) {
                closeSidebar();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && shell.classList.contains('sidebar-open')) {
                closeSidebar();
            }
        });
    };

    const liveFilter = () => {
        document.querySelectorAll('[data-live-filter-input]').forEach((input) => {
            input.addEventListener('input', () => {
                const targetName = input.getAttribute('data-live-filter-input');
                const target = document.querySelector(`[data-live-filter-target="${targetName}"]`);

                if (!target) {
                    return;
                }

                const term = input.value.trim().toLowerCase();
                target.querySelectorAll('[data-live-filter-row]').forEach((row) => {
                    const visible = row.textContent.toLowerCase().includes(term);
                    row.style.display = visible ? '' : 'none';
                });
            });
        });
    };

    const categoryModal = () => {
        const modal = document.querySelector('[data-category-modal]');
        const openButton = document.querySelector('[data-open-category-modal]');
        const closeButton = document.querySelector('[data-close-category-modal]');
        const form = document.querySelector('[data-category-form]');
        const select = document.querySelector('[data-category-select]');

        if (!modal || !openButton || !closeButton || !form || !select) {
            return;
        }

        openButton.addEventListener('click', () => modal.showModal());
        closeButton.addEventListener('click', () => modal.close());

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(form);
            formData.append('_token', csrfToken);

            const response = await fetch('/admin/categories/quick-store', {
                method: 'POST',
                body: formData,
            });

            const payload = await response.json();

            if (!response.ok) {
                Swal.fire('Não foi possível salvar', payload.message ?? 'Erro ao criar categoria.', 'error');
                return;
            }

            const option = document.createElement('option');
            option.value = payload.category.id;
            option.textContent = payload.category.name;
            option.selected = true;
            select.append(option);
            modal.close();
            form.reset();
            Swal.fire('Categoria criada', payload.message, 'success');
        });
    };

    const registerTable = () => {
        const button = document.querySelector('[data-register-table]');
        const form = document.querySelector('[data-register-form]');

        if (!button || !form) {
            return;
        }

        button.addEventListener('click', async () => {
            const result = await Swal.fire({
                title: 'Qual é o seu nome?',
                input: 'text',
                inputPlaceholder: 'Digite o nome para abrir a mesa',
                confirmButtonText: 'Liberar cardápio',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                inputValidator: (value) => value.trim() === '' ? 'Informe seu nome.' : undefined,
            });

            if (!result.isConfirmed) {
                return;
            }

            form.querySelector('input[name="customer_name"]').value = result.value.trim();
            form.submit();
        });
    };

    const repeatableFields = () => {
        document.querySelectorAll('[data-repeatable-root]').forEach((root) => {
            const list = root.querySelector('[data-repeatable-list]');
            const template = root.querySelector('[data-repeatable-template]');
            const addButton = root.querySelector('[data-repeatable-add]');

            if (!list || !template || !addButton) {
                return;
            }

            const bindRow = (row) => {
                row.querySelectorAll('.money-mask').forEach(applyMoneyMask);
                row.querySelectorAll('[data-repeatable-remove]').forEach((button) => {
                    if (button.dataset.bound === '1') {
                        return;
                    }

                    button.dataset.bound = '1';
                    button.addEventListener('click', () => {
                        row.remove();
                    });
                });
            };

            list.querySelectorAll('.repeatable-row').forEach(bindRow);

            addButton.addEventListener('click', () => {
                const fragment = template.content.cloneNode(true);
                const row = fragment.firstElementChild;

                if (!row) {
                    return;
                }

                list.append(row);
                bindRow(row);
                row.querySelector('input')?.focus();
            });
        });
    };

    const stockToggle = () => {
        const toggle = document.querySelector('[data-stock-toggle]');
        const wrapper = document.querySelector('[data-stock-wrapper]');
        const input = document.querySelector('[data-stock-input]');

        if (!toggle || !wrapper || !input) {
            return;
        }

        const sync = () => {
            const enabled = toggle.checked;
            input.disabled = !enabled;
            wrapper.classList.toggle('is-disabled', !enabled);

            if (!enabled) {
                input.value = '0';
            }
        };

        toggle.addEventListener('change', sync);
        sync();
    };

    const imageCropper = async () => {
        const root = document.querySelector('[data-image-cropper]');

        if (!root) {
            return;
        }

        loadStyle('https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css');
        await loadScript('https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js');

        const image = root.querySelector('[data-image-preview-image]');
        const fileInput = root.querySelector('[data-image-input]');
        const previewCard = root.querySelector('[data-image-preview-card]');
        const zoomInput = root.querySelector('[data-image-zoom]');
        const posXInput = root.querySelector('[data-image-position-x]');
        const posYInput = root.querySelector('[data-image-position-y]');

        if (!image || !fileInput || !previewCard || !zoomInput || !posXInput || !posYInput || typeof window.Cropper === 'undefined') {
            return;
        }

        let cropper;

        const syncPreview = () => {
            previewCard.style.setProperty('--img', `url('${image.src}')`);
            previewCard.style.setProperty('--zoom', `${zoomInput.value}%`);
            previewCard.style.setProperty('--pos-x', `${posXInput.value}%`);
            previewCard.style.setProperty('--pos-y', `${posYInput.value}%`);
        };

        const syncFromCropper = () => {
            if (!cropper) {
                return;
            }

            const imageData = cropper.getImageData();
            const containerData = cropper.getContainerData();

            if (!imageData.width || !containerData.width) {
                return;
            }

            const zoom = clamp(Math.round((imageData.width / containerData.width) * 100), 100, 180);
            const posX = clamp(Math.round((((containerData.width / 2) - imageData.left) / imageData.width) * 100), 0, 100);
            const posY = clamp(Math.round((((containerData.height / 2) - imageData.top) / imageData.height) * 100), 0, 100);

            zoomInput.value = String(zoom);
            posXInput.value = String(posX);
            posYInput.value = String(posY);
            syncPreview();
        };

        const bootCropper = () => {
            cropper?.destroy();
            cropper = new window.Cropper(image, {
                viewMode: 1,
                dragMode: 'move',
                aspectRatio: 4 / 3,
                autoCropArea: 1,
                background: false,
                crop: syncFromCropper,
                ready: () => {
                    syncPreview();
                },
            });
        };

        image.addEventListener('load', bootCropper, { once: true });

        if (image.complete) {
            bootCropper();
        }

        fileInput.addEventListener('change', () => {
            const [file] = fileInput.files ?? [];

            if (!file) {
                return;
            }

            const reader = new FileReader();
            reader.onload = () => {
                image.removeEventListener('load', bootCropper);
                image.addEventListener('load', bootCropper, { once: true });
                image.src = String(reader.result ?? '');
            };
            reader.readAsDataURL(file);
        });

        syncPreview();
    };

    const openOrderItemDialog = async (item) => {
        const removableOptions = (item.removable_ingredients ?? []).map((ingredient) => `
            <label style="display:flex; gap:8px; margin:8px 0; text-align:left;">
                <input type="checkbox" name="removed_ingredients" value="${ingredient}">
                <span>${ingredient}</span>
            </label>
        `).join('');
        const additionalsOptions = (item.additionals ?? []).map((additional) => `
            <label style="display:flex; gap:8px; margin:8px 0; text-align:left;">
                <input type="checkbox" name="additionals" value="${additional.name}" data-price="${additional.price}">
                <span>${additional.name} (+${formatMoney(Number(additional.price))})</span>
            </label>
        `).join('');
        const deliverySelect = item.service_group === 'drink'
            ? `
                <label style="display:grid; gap:8px; margin-top:12px; text-align:left;">
                    <span>Entrega da bebida</span>
                    <select id="delivery_timing">
                        <option value="immediate">Entregar agora</option>
                        <option value="with_order">Junto com o restante</option>
                    </select>
                </label>
            `
            : '<input type="hidden" id="delivery_timing" value="with_order">';

        return Swal.fire({
            title: item.title,
            width: 720,
            confirmButtonText: 'Adicionar ao pedido',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            html: `
                <div style="text-align:left; display:grid; gap:16px;">
                    <p>${item.description}</p>
                    <label style="display:grid; gap:8px;">
                        <span>Quantidade</span>
                        <input id="cart_quantity" type="number" min="1" value="1">
                    </label>
                    <div>
                        <strong>Retirar ingredientes</strong>
                        ${removableOptions || '<p>Nenhuma opção configurada.</p>'}
                    </div>
                    <div>
                        <strong>Adicionar extras</strong>
                        ${additionalsOptions || '<p>Nenhum adicional configurado.</p>'}
                    </div>
                    ${deliverySelect}
                    <label style="display:grid; gap:8px;">
                        <span>Observações</span>
                        <textarea id="cart_notes" rows="3" placeholder="Ex.: ponto da carne, sem gelo"></textarea>
                    </label>
                </div>
            `,
            preConfirm: () => {
                const quantity = Number(document.getElementById('cart_quantity')?.value ?? '1');
                const removedIngredients = Array.from(document.querySelectorAll('input[name="removed_ingredients"]:checked')).map((input) => input.value);
                const additionals = Array.from(document.querySelectorAll('input[name="additionals"]:checked')).map((input) => ({
                    name: input.value,
                    price: Number(input.getAttribute('data-price') ?? '0'),
                }));
                const notes = document.getElementById('cart_notes')?.value ?? '';
                const deliveryTiming = document.getElementById('delivery_timing')?.value ?? 'with_order';

                if (!Number.isFinite(quantity) || quantity <= 0) {
                    Swal.showValidationMessage('Informe uma quantidade válida.');
                    return false;
                }

                return {
                    menu_item_id: item.id,
                    title: item.title,
                    quantity,
                    removed_ingredients: removedIngredients,
                    additionals,
                    notes,
                    delivery_timing: deliveryTiming,
                };
            },
        });
    };

    const orderBuilders = () => {
        document.querySelectorAll('[data-order-builder-shell]').forEach((shell) => {
            const cartPayloadInput = shell.querySelector('[data-cart-payload]');
            const cartList = shell.querySelector('[data-cart-list]');
            const submitButton = shell.querySelector('[data-submit-cart]');
            const cartTotal = shell.querySelector('[data-cart-total]');
            const tableSelect = shell.querySelector('[data-order-table-select]');
            const customerInput = shell.querySelector('[data-order-customer-input]');
            const customerHint = shell.querySelector('[data-order-customer-hint]');
            const form = shell.querySelector('form');

            if (!cartPayloadInput || !cartList || !submitButton) {
                return;
            }

            const cart = [];

            const syncCustomer = () => {
                if (!tableSelect || !customerInput) {
                    return;
                }

                const option = tableSelect.options[tableSelect.selectedIndex];
                const hasSession = option?.dataset.hasSession === '1';
                const customerName = option?.dataset.customerName ?? '';

                customerInput.readOnly = hasSession;
                customerInput.required = !hasSession;

                if (hasSession) {
                    customerInput.value = customerName;
                    if (customerHint) {
                        customerHint.textContent = 'Mesa já ocupada. O pedido será lançado na comanda aberta.';
                    }
                } else {
                    if (customerHint) {
                        customerHint.textContent = 'Informe o nome do cliente para abrir a mesa.';
                    }
                }
            };

            const renderCart = () => {
                if (cart.length === 0) {
                    cartList.innerHTML = '<p class="muted">Nenhum item adicionado ainda.</p>';
                    cartPayloadInput.value = '[]';
                    submitButton.disabled = true;

                    if (cartTotal) {
                        cartTotal.textContent = formatMoney(0);
                    }

                    return;
                }

                const total = cart.reduce((sum, item) => {
                    const additionals = item.additionals.reduce((acc, additional) => acc + Number(additional.price || 0), 0);
                    return sum + (Number(item.quantity) * (Number(item.unit_price || item.sale_price || 0) + additionals));
                }, 0);

                cartList.innerHTML = cart.map((item, index) => {
                    const additionals = item.additionals.map((additional) => `${additional.name} (+${formatMoney(Number(additional.price))})`).join(', ');
                    const removed = item.removed_ingredients.join(', ');

                    return `
                        <article class="stack-item">
                            <div>
                                <strong>${item.quantity}x ${item.title}</strong>
                                <p>${removed ? `Retirar: ${removed}` : 'Sem remoções'}</p>
                                <p>${additionals || 'Sem adicionais'}</p>
                                <p>${item.notes || 'Sem observações'}</p>
                            </div>
                            <button type="button" class="button button-danger small" data-remove-cart-item="${index}">Remover</button>
                        </article>
                    `;
                }).join('');

                cartPayloadInput.value = JSON.stringify(cart);
                submitButton.disabled = false;

                if (cartTotal) {
                    cartTotal.textContent = formatMoney(total);
                }

                cartList.querySelectorAll('[data-remove-cart-item]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const index = Number(button.getAttribute('data-remove-cart-item'));
                        cart.splice(index, 1);
                        renderCart();
                    });
                });
            };

            shell.querySelectorAll('[data-order-builder-item]').forEach((button) => {
                button.addEventListener('click', async () => {
                    const item = JSON.parse(button.getAttribute('data-order-builder-item') ?? '{}');
                    const result = await openOrderItemDialog(item);

                    if (!result.isConfirmed) {
                        return;
                    }

                    cart.push({
                        ...result.value,
                        unit_price: Number(item.sale_price ?? 0),
                    });
                    renderCart();
                    Swal.fire('Item adicionado', 'O produto foi incluído no resumo do pedido.', 'success');
                });
            });

            tableSelect?.addEventListener('change', syncCustomer);
            shell.resetOrderBuilder = () => {
                cart.splice(0, cart.length);
                form?.reset();
                syncCustomer();
                renderCart();
            };
            syncCustomer();
            renderCart();
        });
    };

    const adminOrderModal = () => {
        document.querySelectorAll('[data-admin-order-modal]').forEach((modal) => {
            const closeButton = modal.querySelector('[data-close-admin-order-modal]');
            const title = modal.querySelector('[data-order-modal-heading]');
            const flow = modal.querySelector('[data-admin-order-flow]');

            if (!flow) {
                return;
            }

            const form = flow.querySelector('.admin-order-form');
            const tableSelect = flow.querySelector('[data-order-table-select]');
            const tableIdInput = flow.querySelector('[data-order-table-id]');
            const customerNameInput = flow.querySelector('[data-order-customer-name]');
            const redirectInput = flow.querySelector('[data-order-redirect]');
            const cartPayloadInput = flow.querySelector('[data-cart-payload]');
            const submitButtons = flow.querySelectorAll('[data-submit-cart]');
            const cartTotalNodes = flow.querySelectorAll('[data-cart-total]');
            const cartCountNode = flow.querySelector('[data-admin-cart-count]');
            const setupStatus = flow.querySelector('[data-admin-setup-status]');
            const tableChip = flow.querySelector('[data-admin-table-chip]');
            const customerChip = flow.querySelector('[data-admin-customer-chip]');
            const startButton = flow.querySelector('[data-admin-start-order]');
            const backSetupButtons = flow.querySelectorAll('[data-admin-back-setup]');
            const reviewButtons = flow.querySelectorAll('[data-admin-open-review]');
            const backCatalogButtons = flow.querySelectorAll('[data-admin-back-catalog]');
            const reviewList = flow.querySelector('[data-admin-review-list]');
            const reviewTotal = flow.querySelector('[data-admin-review-total]');
            const categoryFilter = flow.querySelector('[data-admin-category-filter]');
            const searchFilter = flow.querySelector('[data-admin-search-filter]');
            const sections = flow.querySelectorAll('[data-admin-category-section]');
            const itemButtons = flow.querySelectorAll('[data-admin-menu-item]');
            const itemOverlay = flow.querySelector('[data-admin-item-overlay]');
            const itemPanel = flow.querySelector('[data-admin-item-panel]');
            const panelTitle = flow.querySelector('[data-admin-panel-title]');
            const panelDescription = flow.querySelector('[data-admin-panel-description]');
            const panelQuantity = flow.querySelector('[data-admin-panel-quantity]');
            const panelRemovables = flow.querySelector('[data-admin-panel-removables]');
            const panelAdditionals = flow.querySelector('[data-admin-panel-additionals]');
            const panelDeliveryWrapper = flow.querySelector('[data-admin-panel-delivery-wrapper]');
            const panelDelivery = flow.querySelector('[data-admin-panel-delivery]');
            const panelNotes = flow.querySelector('[data-admin-panel-notes]');
            const panelTotal = flow.querySelector('[data-admin-panel-total]');
            const panelSave = flow.querySelector('[data-admin-panel-save]');
            const panelCloseButtons = flow.querySelectorAll('[data-admin-panel-close]');
            const steps = {
                setup: flow.querySelector('[data-admin-step="setup"]'),
                catalog: flow.querySelector('[data-admin-step="catalog"]'),
                review: flow.querySelector('[data-admin-step="review"]'),
            };

            const state = {
                cart: [],
                tableId: '',
                customerName: '',
                redirectTo: redirectInput?.value ?? '/admin/orders',
                selectedItem: null,
                editingIndex: null,
            };

            const parseItem = (button) => JSON.parse(button.getAttribute('data-admin-menu-item') ?? '{}');
            const selectedOption = () => tableSelect?.options[tableSelect.selectedIndex] ?? null;
            const tableLabel = () => selectedOption()?.textContent?.trim() ?? 'Mesa não definida';
            const orderTotal = () => state.cart.reduce((sum, item) => {
                const additionals = (item.additionals ?? []).reduce((acc, additional) => acc + Number(additional.price || 0), 0);
                return sum + (Number(item.quantity || 0) * (Number(item.unit_price || 0) + additionals));
            }, 0);

            const updateHeader = () => {
                if (tableChip) {
                    tableChip.textContent = state.tableId !== '' ? tableLabel() : 'Mesa não definida';
                }

                if (customerChip) {
                    customerChip.textContent = state.customerName !== '' ? state.customerName : 'Cliente não definido';
                }

                if (tableIdInput) {
                    tableIdInput.value = state.tableId;
                }

                if (customerNameInput) {
                    customerNameInput.value = state.customerName;
                }

                if (redirectInput) {
                    redirectInput.value = state.redirectTo;
                }
            };

            const switchStep = (stepName) => {
                Object.entries(steps).forEach(([name, element]) => {
                    if (!element) {
                        return;
                    }

                    element.classList.toggle('is-hidden', name !== stepName);
                });
            };

            const syncSetupStatus = () => {
                const option = selectedOption();

                if (!setupStatus || !option || option.value === '') {
                    if (setupStatus) {
                        setupStatus.textContent = 'Selecione uma mesa para continuar.';
                    }
                    return;
                }

                setupStatus.textContent = option.dataset.hasSession === '1'
                    ? `Comanda aberta para ${option.dataset.customerName || 'cliente em atendimento'}.`
                    : 'Mesa livre. O nome do cliente será solicitado antes da abertura do cardápio.';
            };

            const renderCatalog = () => {
                const selectedCategory = categoryFilter?.value ?? '';
                const term = (searchFilter?.value ?? '').trim().toLowerCase();

                sections.forEach((section) => {
                    let visibleCards = 0;

                    section.querySelectorAll('[data-admin-menu-card]').forEach((card) => {
                        const matchesCategory = selectedCategory === '' || card.getAttribute('data-category-name') === selectedCategory;
                        const matchesSearch = term === '' || (card.getAttribute('data-search-text') ?? '').includes(term);
                        const visible = matchesCategory && matchesSearch;
                        card.style.display = visible ? '' : 'none';
                        if (visible) {
                            visibleCards += 1;
                        }
                    });

                    section.style.display = visibleCards > 0 ? '' : 'none';
                });
            };

            const renderReview = () => {
                if (!reviewList) {
                    return;
                }

                if (state.cart.length === 0) {
                    reviewList.innerHTML = '<p class="muted">Nenhum item adicionado ainda.</p>';
                    return;
                }

                reviewList.innerHTML = state.cart.map((item, index) => {
                    const removed = (item.removed_ingredients ?? []).join(', ');
                    const additionals = (item.additionals ?? []).map((additional) => `${additional.name} (+${formatMoney(additional.price)})`).join(', ');
                    const lineTotal = Number(item.quantity || 0) * (Number(item.unit_price || 0) + (item.additionals ?? []).reduce((acc, additional) => acc + Number(additional.price || 0), 0));

                    return `
                        <article class="stack-item admin-review-item">
                            <div class="admin-review-meta">
                                <strong>${item.quantity}x ${item.title}</strong>
                                <p>${removed ? `Retirar: ${removed}` : 'Sem remoções'}</p>
                                <p>${additionals || 'Sem adicionais'}</p>
                                <p>${item.notes || 'Sem observações'}</p>
                                <p>${item.delivery_timing === 'immediate' ? 'Entrega imediata' : 'Entregar com o pedido'}</p>
                            </div>
                            <div class="toolbar-actions">
                                <span class="badge badge-warning">${formatMoney(lineTotal)}</span>
                                <button type="button" class="button button-ghost small" data-admin-edit-item="${index}">Alterar</button>
                                <button type="button" class="button button-danger small" data-admin-remove-item="${index}">Excluir</button>
                            </div>
                        </article>
                    `;
                }).join('');

                reviewList.querySelectorAll('[data-admin-edit-item]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const index = Number(button.getAttribute('data-admin-edit-item'));
                        openItemPanel(state.cart[index], index);
                    });
                });

                reviewList.querySelectorAll('[data-admin-remove-item]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const index = Number(button.getAttribute('data-admin-remove-item'));
                        state.cart.splice(index, 1);
                        renderCartState();
                    });
                });
            };

            const renderCartState = () => {
                const total = orderTotal();
                const count = state.cart.reduce((sum, item) => sum + Number(item.quantity || 0), 0);

                if (cartPayloadInput) {
                    cartPayloadInput.value = JSON.stringify(state.cart);
                }

                cartTotalNodes.forEach((node) => {
                    node.textContent = formatMoney(total);
                });

                if (reviewTotal) {
                    reviewTotal.textContent = formatMoney(total);
                }

                if (cartCountNode) {
                    cartCountNode.textContent = `${count} ${count === 1 ? 'item' : 'itens'}`;
                }

                const enabled = state.cart.length > 0;
                reviewButtons.forEach((button) => {
                    button.disabled = !enabled;
                });
                submitButtons.forEach((button) => {
                    button.disabled = !enabled;
                });

                renderReview();
                updateHeader();
            };

            const closeItemPanel = () => {
                itemOverlay?.classList.add('is-hidden');
                itemPanel?.classList.add('is-hidden');
                state.selectedItem = null;
                state.editingIndex = null;
            };

            const updateItemTotal = () => {
                if (!state.selectedItem || !panelTotal) {
                    return;
                }

                const quantity = Math.max(1, Number(panelQuantity?.value ?? 1));
                const additionals = Array.from(panelAdditionals?.querySelectorAll('input:checked') ?? []).reduce((acc, input) => acc + Number(input.getAttribute('data-price') ?? '0'), 0);
                panelTotal.textContent = formatMoney(quantity * (Number(state.selectedItem.sale_price || 0) + additionals));
            };

            const openItemPanel = (item, index = null) => {
                state.selectedItem = item;
                state.editingIndex = index;

                if (!itemPanel || !itemOverlay || !panelTitle || !panelDescription || !panelQuantity || !panelRemovables || !panelAdditionals || !panelDeliveryWrapper || !panelDelivery || !panelNotes || !panelSave) {
                    return;
                }

                panelTitle.textContent = item.title;
                panelDescription.textContent = item.description ?? '';
                panelQuantity.value = String(item.quantity ?? 1);
                panelNotes.value = item.notes ?? '';
                panelDelivery.value = item.delivery_timing ?? 'with_order';
                panelSave.textContent = index === null ? 'Incluir no pedido' : 'Salvar alterações';
                panelDeliveryWrapper.classList.toggle('is-hidden', item.service_group !== 'drink');

                panelRemovables.innerHTML = item.available_removals?.length
                    ? item.available_removals.map((ingredient) => `
                        <label class="stack-item admin-check-row">
                            <span>${ingredient}</span>
                            <input type="checkbox" value="${ingredient}" ${item.removed_ingredients?.includes(ingredient) ? 'checked' : ''}>
                        </label>
                    `).join('')
                    : '<p class="muted">Nenhuma remoção disponível para este item.</p>';

                panelAdditionals.innerHTML = item.available_additionals?.length
                    ? item.available_additionals.map((additional) => {
                        const checked = (item.additionals ?? []).some((entry) => entry.name === additional.name);
                        return `
                            <label class="stack-item admin-check-row">
                                <span>${additional.name} (+${formatMoney(additional.price)})</span>
                                <input type="checkbox" value="${additional.name}" data-price="${Number(additional.price)}" ${checked ? 'checked' : ''}>
                            </label>
                        `;
                    }).join('')
                    : '<p class="muted">Nenhum adicional disponível para este item.</p>';

                panelAdditionals.querySelectorAll('input').forEach((input) => {
                    input.addEventListener('change', updateItemTotal);
                });
                panelQuantity.removeEventListener('input', updateItemTotal);
                panelQuantity.addEventListener('input', updateItemTotal);
                updateItemTotal();

                itemOverlay.classList.remove('is-hidden');
                itemPanel.classList.remove('is-hidden');
            };

            const saveItemPanel = () => {
                if (!state.selectedItem || !panelQuantity || !panelRemovables || !panelAdditionals || !panelNotes || !panelDelivery) {
                    return;
                }

                const nextItem = {
                    ...state.selectedItem,
                    quantity: Math.max(1, Number(panelQuantity.value || 1)),
                    removed_ingredients: Array.from(panelRemovables.querySelectorAll('input:checked')).map((input) => input.value),
                    additionals: Array.from(panelAdditionals.querySelectorAll('input:checked')).map((input) => ({
                        name: input.value,
                        price: Number(input.getAttribute('data-price') ?? '0'),
                    })),
                    notes: panelNotes.value.trim(),
                    delivery_timing: state.selectedItem.service_group === 'drink' ? panelDelivery.value : 'with_order',
                    unit_price: Number(state.selectedItem.sale_price || state.selectedItem.unit_price || 0),
                };

                if (state.editingIndex === null) {
                    state.cart.push(nextItem);
                } else {
                    state.cart[state.editingIndex] = nextItem;
                }

                closeItemPanel();
                renderCartState();
            };

            const askCustomerName = async () => {
                const result = await Swal.fire({
                    title: 'Nome do cliente',
                    input: 'text',
                    inputPlaceholder: 'Digite o nome para abrir a mesa',
                    confirmButtonText: 'Continuar para o cardápio',
                    showCancelButton: true,
                    cancelButtonText: 'Cancelar',
                    inputValidator: (value) => value.trim() === '' ? 'Informe o nome do cliente.' : undefined,
                });

                if (!result.isConfirmed) {
                    return false;
                }

                state.customerName = result.value.trim();
                return true;
            };

            const continueToCatalog = async () => {
                const option = selectedOption();

                if (!option || option.value === '') {
                    Swal.fire('Mesa obrigatória', 'Selecione uma mesa para continuar.', 'warning');
                    return;
                }

                state.tableId = option.value;

                if (option.dataset.hasSession === '1') {
                    state.customerName = option.dataset.customerName ?? '';
                } else if (state.customerName === '') {
                    const confirmed = await askCustomerName();

                    if (!confirmed) {
                        return;
                    }
                }

                updateHeader();
                switchStep('catalog');
            };

            const resetState = () => {
                state.cart = [];
                state.tableId = '';
                state.customerName = '';
                state.selectedItem = null;
                state.editingIndex = null;
                form?.reset();
                closeItemPanel();
                syncSetupStatus();
                renderCatalog();
                renderCartState();
                switchStep('setup');
            };

            closeButton?.addEventListener('click', () => {
                closeItemPanel();
                modal.close();
            });
            itemOverlay?.addEventListener('click', closeItemPanel);
            panelCloseButtons.forEach((button) => button.addEventListener('click', closeItemPanel));
            panelSave?.addEventListener('click', saveItemPanel);
            tableSelect?.addEventListener('change', () => {
                state.tableId = tableSelect.value;
                state.customerName = '';
                syncSetupStatus();
                updateHeader();
            });
            startButton?.addEventListener('click', continueToCatalog);
            backSetupButtons.forEach((button) => button.addEventListener('click', () => switchStep('setup')));
            backCatalogButtons.forEach((button) => button.addEventListener('click', () => switchStep('catalog')));
            reviewButtons.forEach((button) => button.addEventListener('click', () => {
                if (state.cart.length === 0) {
                    return;
                }

                renderReview();
                switchStep('review');
            }));
            categoryFilter?.addEventListener('change', renderCatalog);
            searchFilter?.addEventListener('input', renderCatalog);
            itemButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const item = parseItem(button);
                    openItemPanel({
                        ...item,
                        unit_price: Number(item.sale_price ?? 0),
                        quantity: 1,
                        removed_ingredients: [],
                        additionals: [],
                        notes: '',
                        delivery_timing: item.service_group === 'drink' ? 'immediate' : 'with_order',
                        available_removals: item.removable_ingredients ?? [],
                        available_additionals: item.additionals ?? [],
                    });
                });
            });

            document.querySelectorAll('[data-open-admin-order-modal]').forEach((button) => {
                button.addEventListener('click', async () => {
                    const buttonTableId = button.getAttribute('data-table-id') ?? '';
                    const buttonCustomerName = button.getAttribute('data-customer-name') ?? '';
                    const redirectTo = button.getAttribute('data-redirect-to') ?? '/admin/orders';

                    resetState();
                    state.redirectTo = redirectTo;

                    if (title && button.textContent.trim() !== '') {
                        title.textContent = button.textContent.trim();
                    }

                    if (buttonTableId && tableSelect) {
                        tableSelect.value = buttonTableId;
                        state.tableId = buttonTableId;
                        syncSetupStatus();

                        if (buttonCustomerName !== '') {
                            state.customerName = buttonCustomerName;
                            updateHeader();
                            modal.showModal();
                            switchStep('catalog');
                            return;
                        }

                        const confirmed = await askCustomerName();

                        if (!confirmed) {
                            return;
                        }

                        updateHeader();
                        modal.showModal();
                        switchStep('catalog');
                        return;
                    }

                    updateHeader();
                    modal.showModal();
                    switchStep('setup');
                });
            });

            form?.addEventListener('submit', (event) => {
                if (state.cart.length === 0 || state.tableId === '' || state.customerName === '') {
                    event.preventDefault();
                    Swal.fire('Pedido incompleto', 'Defina mesa, cliente e ao menos um item antes de confirmar.', 'warning');
                    return;
                }

                if (cartPayloadInput) {
                    cartPayloadInput.value = JSON.stringify(state.cart);
                }
                if (tableIdInput) {
                    tableIdInput.value = state.tableId;
                }
                if (customerNameInput) {
                    customerNameInput.value = state.customerName;
                }
            });

            syncSetupStatus();
            renderCatalog();
            renderCartState();
            updateHeader();
        });
    };

    const clientGuidedOrder = () => {
        document.querySelectorAll('[data-client-order-flow]').forEach((flow) => {
            const form = flow;
            const cartPayloadInput = flow.querySelector('[data-client-cart-payload]');
            const categoryFilter = flow.querySelector('[data-client-category-filter]');
            const searchFilter = flow.querySelector('[data-client-search-filter]');
            const sections = flow.querySelectorAll('[data-client-category-section]');
            const itemButtons = flow.querySelectorAll('[data-client-menu-item]');
            const openReviewButton = flow.querySelector('[data-client-open-review]');
            const cartCountNode = flow.querySelector('[data-client-cart-count]');
            const cartTotalNode = flow.querySelector('[data-client-cart-total]');
            const itemOverlay = flow.querySelector('[data-client-item-overlay]');
            const itemPanel = flow.querySelector('[data-client-item-panel]');
            const panelTitle = flow.querySelector('[data-client-panel-title]');
            const panelDescription = flow.querySelector('[data-client-panel-description]');
            const panelQuantity = flow.querySelector('[data-client-panel-quantity]');
            const panelRemovables = flow.querySelector('[data-client-panel-removables]');
            const panelAdditionals = flow.querySelector('[data-client-panel-additionals]');
            const panelDeliveryWrapper = flow.querySelector('[data-client-panel-delivery-wrapper]');
            const panelDelivery = flow.querySelector('[data-client-panel-delivery]');
            const panelNotes = flow.querySelector('[data-client-panel-notes]');
            const panelTotal = flow.querySelector('[data-client-panel-total]');
            const panelSave = flow.querySelector('[data-client-panel-save]');
            const panelCloseButtons = flow.querySelectorAll('[data-client-panel-close]');
            const reviewOverlay = flow.querySelector('[data-client-review-overlay]');
            const reviewPanel = flow.querySelector('[data-client-review-panel]');
            const reviewList = flow.querySelector('[data-client-review-list]');
            const reviewTotal = flow.querySelector('[data-client-review-total]');
            const closeReviewButtons = flow.querySelectorAll('[data-client-close-review]');
            const submitButton = flow.querySelector('[data-client-submit-cart]');

            if (!cartPayloadInput || !openReviewButton || !itemOverlay || !itemPanel || !reviewOverlay || !reviewPanel || !submitButton) {
                return;
            }

            const state = {
                cart: [],
                selectedItem: null,
                editingIndex: null,
            };

            const parseItem = (button) => JSON.parse(button.getAttribute('data-client-menu-item') ?? '{}');

            const orderTotal = () => state.cart.reduce((sum, item) => {
                const additionals = (item.additionals ?? []).reduce((acc, additional) => acc + Number(additional.price || 0), 0);
                return sum + (Number(item.quantity || 0) * (Number(item.unit_price || 0) + additionals));
            }, 0);

            const renderCatalog = () => {
                const selectedCategory = categoryFilter?.value ?? '';
                const term = (searchFilter?.value ?? '').trim().toLowerCase();

                sections.forEach((section) => {
                    let visibleCards = 0;

                    section.querySelectorAll('[data-client-menu-card]').forEach((card) => {
                        const matchesCategory = selectedCategory === '' || card.getAttribute('data-category-name') === selectedCategory;
                        const matchesSearch = term === '' || (card.getAttribute('data-search-text') ?? '').includes(term);
                        const visible = matchesCategory && matchesSearch;
                        card.style.display = visible ? '' : 'none';

                        if (visible) {
                            visibleCards += 1;
                        }
                    });

                    section.style.display = visibleCards > 0 ? '' : 'none';
                });
            };

            const closeItemPanel = () => {
                itemOverlay.classList.add('is-hidden');
                itemPanel.classList.add('is-hidden');
                state.selectedItem = null;
                state.editingIndex = null;
            };

            const closeReviewPanel = () => {
                reviewOverlay.classList.add('is-hidden');
                reviewPanel.classList.add('is-hidden');
            };

            const updateItemTotal = () => {
                if (!state.selectedItem || !panelTotal) {
                    return;
                }

                const quantity = Math.max(1, Number(panelQuantity?.value ?? 1));
                const additionals = Array.from(panelAdditionals?.querySelectorAll('input:checked') ?? []).reduce((acc, input) => acc + Number(input.getAttribute('data-price') ?? '0'), 0);
                panelTotal.textContent = formatMoney(quantity * (Number(state.selectedItem.sale_price || 0) + additionals));
            };

            const openItemPanel = (item, index = null) => {
                state.selectedItem = item;
                state.editingIndex = index;

                if (!panelTitle || !panelDescription || !panelQuantity || !panelRemovables || !panelAdditionals || !panelDeliveryWrapper || !panelDelivery || !panelNotes || !panelSave) {
                    return;
                }

                closeReviewPanel();
                panelTitle.textContent = item.title;
                panelDescription.textContent = item.description ?? '';
                panelQuantity.value = String(item.quantity ?? 1);
                panelNotes.value = item.notes ?? '';
                panelDelivery.value = item.delivery_timing ?? 'with_order';
                panelSave.textContent = index === null ? 'Incluir no pedido' : 'Salvar alterações';
                panelDeliveryWrapper.classList.toggle('is-hidden', item.service_group !== 'drink');

                panelRemovables.innerHTML = item.available_removals?.length
                    ? item.available_removals.map((ingredient) => `
                        <label class="stack-item client-check-row">
                            <span>${ingredient}</span>
                            <input type="checkbox" value="${ingredient}" ${item.removed_ingredients?.includes(ingredient) ? 'checked' : ''}>
                        </label>
                    `).join('')
                    : '<p class="muted">Nenhuma remoção disponível para este item.</p>';

                panelAdditionals.innerHTML = item.available_additionals?.length
                    ? item.available_additionals.map((additional) => {
                        const checked = (item.additionals ?? []).some((entry) => entry.name === additional.name);
                        return `
                            <label class="stack-item client-check-row">
                                <span>${additional.name} (+${formatMoney(additional.price)})</span>
                                <input type="checkbox" value="${additional.name}" data-price="${Number(additional.price)}" ${checked ? 'checked' : ''}>
                            </label>
                        `;
                    }).join('')
                    : '<p class="muted">Nenhum adicional disponível para este item.</p>';

                panelAdditionals.querySelectorAll('input').forEach((input) => {
                    input.addEventListener('change', updateItemTotal);
                });
                panelQuantity.removeEventListener('input', updateItemTotal);
                panelQuantity.addEventListener('input', updateItemTotal);
                updateItemTotal();

                itemOverlay.classList.remove('is-hidden');
                itemPanel.classList.remove('is-hidden');
            };

            const renderReview = () => {
                if (!reviewList) {
                    return;
                }

                if (state.cart.length === 0) {
                    reviewList.innerHTML = '<p class="muted">Nenhum item adicionado ainda.</p>';
                    return;
                }

                reviewList.innerHTML = state.cart.map((item, index) => {
                    const removed = (item.removed_ingredients ?? []).join(', ');
                    const additionals = (item.additionals ?? []).map((additional) => `${additional.name} (+${formatMoney(additional.price)})`).join(', ');
                    const lineTotal = Number(item.quantity || 0) * (Number(item.unit_price || 0) + (item.additionals ?? []).reduce((acc, additional) => acc + Number(additional.price || 0), 0));

                    return `
                        <article class="stack-item client-review-item">
                            <div class="client-review-meta">
                                <strong>${item.quantity}x ${item.title}</strong>
                                <p>${removed ? `Retirar: ${removed}` : 'Sem remoções'}</p>
                                <p>${additionals || 'Sem adicionais'}</p>
                                <p>${item.notes || 'Sem observações'}</p>
                                <p>${item.delivery_timing === 'immediate' ? 'Entrega imediata' : 'Entregar com o pedido'}</p>
                            </div>
                            <div class="toolbar-actions">
                                <span class="badge badge-warning">${formatMoney(lineTotal)}</span>
                                <button type="button" class="button button-ghost small" data-client-edit-item="${index}">Alterar</button>
                                <button type="button" class="button button-danger small" data-client-remove-item="${index}">Excluir</button>
                            </div>
                        </article>
                    `;
                }).join('');

                reviewList.querySelectorAll('[data-client-edit-item]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const index = Number(button.getAttribute('data-client-edit-item'));
                        openItemPanel(state.cart[index], index);
                    });
                });

                reviewList.querySelectorAll('[data-client-remove-item]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const index = Number(button.getAttribute('data-client-remove-item'));
                        state.cart.splice(index, 1);
                        renderCartState();
                    });
                });
            };

            const renderCartState = () => {
                const total = orderTotal();
                const count = state.cart.reduce((sum, item) => sum + Number(item.quantity || 0), 0);

                cartPayloadInput.value = JSON.stringify(state.cart);
                if (cartCountNode) {
                    cartCountNode.textContent = `${count} ${count === 1 ? 'item' : 'itens'}`;
                }
                if (cartTotalNode) {
                    cartTotalNode.textContent = formatMoney(total);
                }
                if (reviewTotal) {
                    reviewTotal.textContent = formatMoney(total);
                }

                const enabled = state.cart.length > 0;
                openReviewButton.disabled = !enabled;
                submitButton.disabled = !enabled;

                renderReview();
            };

            const saveItemPanel = () => {
                if (!state.selectedItem || !panelQuantity || !panelRemovables || !panelAdditionals || !panelNotes || !panelDelivery) {
                    return;
                }

                const nextItem = {
                    ...state.selectedItem,
                    quantity: Math.max(1, Number(panelQuantity.value || 1)),
                    removed_ingredients: Array.from(panelRemovables.querySelectorAll('input:checked')).map((input) => input.value),
                    additionals: Array.from(panelAdditionals.querySelectorAll('input:checked')).map((input) => ({
                        name: input.value,
                        price: Number(input.getAttribute('data-price') ?? '0'),
                    })),
                    notes: panelNotes.value.trim(),
                    delivery_timing: state.selectedItem.service_group === 'drink' ? panelDelivery.value : 'with_order',
                    unit_price: Number(state.selectedItem.sale_price || state.selectedItem.unit_price || 0),
                };

                if (state.editingIndex === null) {
                    state.cart.push(nextItem);
                } else {
                    state.cart[state.editingIndex] = nextItem;
                }

                closeItemPanel();
                renderCartState();
            };

            categoryFilter?.addEventListener('change', renderCatalog);
            searchFilter?.addEventListener('input', renderCatalog);
            itemButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const item = parseItem(button);
                    openItemPanel({
                        ...item,
                        unit_price: Number(item.sale_price ?? 0),
                        quantity: 1,
                        removed_ingredients: [],
                        additionals: [],
                        notes: '',
                        delivery_timing: item.service_group === 'drink' ? 'immediate' : 'with_order',
                        available_removals: item.removable_ingredients ?? [],
                        available_additionals: item.additionals ?? [],
                    });
                });
            });
            itemOverlay.addEventListener('click', closeItemPanel);
            panelCloseButtons.forEach((button) => button.addEventListener('click', closeItemPanel));
            panelSave?.addEventListener('click', saveItemPanel);
            openReviewButton.addEventListener('click', () => {
                if (state.cart.length === 0) {
                    return;
                }

                closeItemPanel();
                reviewOverlay.classList.remove('is-hidden');
                reviewPanel.classList.remove('is-hidden');
            });
            reviewOverlay.addEventListener('click', closeReviewPanel);
            closeReviewButtons.forEach((button) => button.addEventListener('click', closeReviewPanel));
            form.addEventListener('submit', (event) => {
                if (state.cart.length === 0) {
                    event.preventDefault();
                    Swal.fire('Pedido vazio', 'Adicione ao menos um item antes de confirmar.', 'warning');
                    return;
                }

                cartPayloadInput.value = JSON.stringify(state.cart);
            });

            renderCatalog();
            renderCartState();
        });
    };

    const clientCart = () => {
        return undefined;
    };

    const init = () => {
        mobileSidebar();
        themeToggle();
        moneyMasks();
        liveFilter();
        categoryModal();
        registerTable();
        repeatableFields();
        stockToggle();
        imageCropper();
        orderBuilders();
        adminOrderModal();
        clientGuidedOrder();
        clientCart();
    };

    return { init };
})();

document.addEventListener('DOMContentLoaded', TechFood.init);
