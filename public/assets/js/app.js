/**
 * Jira Clone - Main JavaScript
 */

(function() {
    'use strict';

    // ==========================================
    // Constants & Configuration
    // ==========================================
    const API_BASE = '/api/v1';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ==========================================
    // API Helper
    // ==========================================
    const api = {
        async request(url, options = {}) {
            const defaults = {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            };

            const config = { ...defaults, ...options };
            if (options.headers) {
                config.headers = { ...defaults.headers, ...options.headers };
            }

            try {
                const response = await fetch(url, config);
                const data = await response.json();

                if (!response.ok) {
                    throw { status: response.status, data };
                }

                return data;
            } catch (error) {
                console.error('API Error:', error);
                throw error;
            }
        },

        get(url) {
            return this.request(url);
        },

        post(url, data) {
            return this.request(url, {
                method: 'POST',
                body: JSON.stringify(data),
            });
        },

        put(url, data) {
            return this.request(url, {
                method: 'PUT',
                body: JSON.stringify(data),
            });
        },

        delete(url) {
            return this.request(url, {
                method: 'DELETE',
            });
        },
    };

    // ==========================================
    // Toast Notifications
    // ==========================================
    const toast = {
        container: null,

        init() {
            this.container = document.createElement('div');
            this.container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            this.container.style.zIndex = '1100';
            document.body.appendChild(this.container);
        },

        show(message, type = 'info') {
            if (!this.container) this.init();

            const icons = {
                success: 'check-circle-fill',
                error: 'exclamation-circle-fill',
                warning: 'exclamation-triangle-fill',
                info: 'info-circle-fill',
            };

            const colors = {
                success: 'text-success',
                error: 'text-danger',
                warning: 'text-warning',
                info: 'text-primary',
            };

            const toastEl = document.createElement('div');
            toastEl.className = 'toast show';
            toastEl.setAttribute('role', 'alert');
            toastEl.innerHTML = `
                <div class="toast-header">
                    <i class="bi bi-${icons[type]} ${colors[type]} me-2"></i>
                    <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">${message}</div>
            `;

            this.container.appendChild(toastEl);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                toastEl.classList.remove('show');
                setTimeout(() => toastEl.remove(), 150);
            }, 5000);

            // Close button
            toastEl.querySelector('.btn-close').addEventListener('click', () => {
                toastEl.classList.remove('show');
                setTimeout(() => toastEl.remove(), 150);
            });
        },

        success(message) { this.show(message, 'success'); },
        error(message) { this.show(message, 'error'); },
        warning(message) { this.show(message, 'warning'); },
        info(message) { this.show(message, 'info'); },
    };

    // ==========================================
    // Drag and Drop for Boards
    // ==========================================
    const boardDragDrop = {
        draggedCard: null,
        sourceColumn: null,

        init() {
            document.querySelectorAll('.board-card').forEach(card => {
                card.setAttribute('draggable', 'true');
                card.addEventListener('dragstart', this.handleDragStart.bind(this));
                card.addEventListener('dragend', this.handleDragEnd.bind(this));
            });

            document.querySelectorAll('.board-column-content').forEach(column => {
                column.addEventListener('dragover', this.handleDragOver.bind(this));
                column.addEventListener('drop', this.handleDrop.bind(this));
                column.addEventListener('dragleave', this.handleDragLeave.bind(this));
            });
        },

        handleDragStart(e) {
            this.draggedCard = e.target;
            this.sourceColumn = e.target.closest('.board-column');
            e.target.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', e.target.dataset.issueId);
        },

        handleDragEnd(e) {
            e.target.classList.remove('dragging');
            document.querySelectorAll('.board-column-content').forEach(col => {
                col.classList.remove('drag-over');
            });
        },

        handleDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            e.currentTarget.classList.add('drag-over');
        },

        handleDragLeave(e) {
            e.currentTarget.classList.remove('drag-over');
        },

        async handleDrop(e) {
            e.preventDefault();
            e.currentTarget.classList.remove('drag-over');

            const targetColumn = e.currentTarget.closest('.board-column');
            const issueId = e.dataTransfer.getData('text/plain');
            const statusId = targetColumn.dataset.statusId;

            if (this.sourceColumn === targetColumn) return;

            // Move card visually
            e.currentTarget.appendChild(this.draggedCard);

            // Update via API
            try {
                const boardId = document.querySelector('.board-container').dataset.boardId;
                await api.post(`/boards/${boardId}/move`, {
                    issue_id: issueId,
                    status_id: statusId,
                });
                toast.success('Issue moved successfully');
            } catch (error) {
                // Revert on error
                this.sourceColumn.querySelector('.board-column-content').appendChild(this.draggedCard);
                toast.error('Failed to move issue');
            }
        },
    };

    // ==========================================
    // Quick Search
    // ==========================================
    const quickSearch = {
        input: null,
        results: null,
        timeout: null,

        init() {
            this.input = document.querySelector('#quickSearchInput');
            if (!this.input) return;

            this.results = document.createElement('div');
            this.results.className = 'search-results d-none';
            this.input.parentNode.appendChild(this.results);

            this.input.addEventListener('input', this.handleInput.bind(this));
            this.input.addEventListener('focus', this.handleFocus.bind(this));
            
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.search-container')) {
                    this.hideResults();
                }
            });
        },

        handleInput(e) {
            clearTimeout(this.timeout);
            const query = e.target.value.trim();

            if (query.length < 2) {
                this.hideResults();
                return;
            }

            this.timeout = setTimeout(() => this.search(query), 300);
        },

        handleFocus(e) {
            if (e.target.value.trim().length >= 2) {
                this.results.classList.remove('d-none');
            }
        },

        async search(query) {
            try {
                const data = await api.get(`/search/quick?q=${encodeURIComponent(query)}`);
                this.showResults(data.results || []);
            } catch (error) {
                console.error('Search error:', error);
            }
        },

        showResults(results) {
            if (results.length === 0) {
                this.results.innerHTML = '<div class="p-3 text-muted">No results found</div>';
            } else {
                this.results.innerHTML = results.map(r => `
                    <a href="/issue/${r.issue_key}" class="search-result-item">
                        <span class="badge" style="background: ${r.issue_type_color}">${r.issue_type_icon}</span>
                        <span class="fw-medium text-primary">${r.issue_key}</span>
                        <span class="text-truncate">${this.escapeHtml(r.summary)}</span>
                    </a>
                `).join('');
            }
            this.results.classList.remove('d-none');
        },

        hideResults() {
            this.results.classList.add('d-none');
        },

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },
    };

    // ==========================================
    // Markdown Editor
    // ==========================================
    const markdownEditor = {
        init() {
            document.querySelectorAll('.markdown-editor').forEach(editor => {
                this.setupEditor(editor);
            });
        },

        setupEditor(editor) {
            const textarea = editor.querySelector('textarea');
            const preview = editor.querySelector('.markdown-preview');
            const toolbar = editor.querySelector('.markdown-editor-toolbar');

            if (toolbar) {
                toolbar.querySelectorAll('button').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.handleAction(textarea, btn.dataset.action);
                    });
                });
            }

            // Tab support
            textarea.addEventListener('keydown', (e) => {
                if (e.key === 'Tab') {
                    e.preventDefault();
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    textarea.value = textarea.value.substring(0, start) + '    ' + textarea.value.substring(end);
                    textarea.selectionStart = textarea.selectionEnd = start + 4;
                }
            });

            // Preview toggle
            const previewBtn = editor.querySelector('[data-action="preview"]');
            if (previewBtn) {
                previewBtn.addEventListener('click', () => {
                    if (preview.classList.contains('d-none')) {
                        preview.innerHTML = this.parseMarkdown(textarea.value);
                        preview.classList.remove('d-none');
                        textarea.classList.add('d-none');
                    } else {
                        preview.classList.add('d-none');
                        textarea.classList.remove('d-none');
                    }
                });
            }
        },

        handleAction(textarea, action) {
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            const selected = text.substring(start, end);

            let replacement = '';
            let cursorOffset = 0;

            switch (action) {
                case 'bold':
                    replacement = `**${selected || 'bold text'}**`;
                    cursorOffset = selected ? 0 : -2;
                    break;
                case 'italic':
                    replacement = `*${selected || 'italic text'}*`;
                    cursorOffset = selected ? 0 : -1;
                    break;
                case 'code':
                    replacement = `\`${selected || 'code'}\``;
                    cursorOffset = selected ? 0 : -1;
                    break;
                case 'link':
                    replacement = `[${selected || 'link text'}](url)`;
                    cursorOffset = selected ? -1 : -5;
                    break;
                case 'list':
                    replacement = `\n- ${selected || 'list item'}`;
                    break;
                case 'heading':
                    replacement = `\n## ${selected || 'Heading'}`;
                    break;
            }

            textarea.value = text.substring(0, start) + replacement + text.substring(end);
            textarea.selectionStart = textarea.selectionEnd = start + replacement.length + cursorOffset;
            textarea.focus();
        },

        parseMarkdown(text) {
            // Basic markdown parsing
            text = this.escapeHtml(text);
            text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            text = text.replace(/\*(.+?)\*/g, '<em>$1</em>');
            text = text.replace(/`(.+?)`/g, '<code>$1</code>');
            text = text.replace(/\[(.+?)\]\((.+?)\)/g, '<a href="$2" target="_blank">$1</a>');
            text = text.replace(/^## (.+)$/gm, '<h4>$1</h4>');
            text = text.replace(/^- (.+)$/gm, '<li>$1</li>');
            text = text.replace(/\n/g, '<br>');
            return text;
        },

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },
    };

    // ==========================================
    // Issue Form Handling
    // ==========================================
    const issueForm = {
        init() {
            const form = document.querySelector('#issueForm');
            if (!form) return;

            // Project change handler
            const projectSelect = form.querySelector('[name="project_id"]');
            if (projectSelect) {
                projectSelect.addEventListener('change', this.loadProjectOptions.bind(this));
            }

            // Assignee autocomplete
            const assigneeInput = form.querySelector('[name="assignee_search"]');
            if (assigneeInput) {
                this.setupAutocomplete(assigneeInput, 'users');
            }
        },

        async loadProjectOptions(e) {
            const projectId = e.target.value;
            if (!projectId) return;

            try {
                const project = await api.get(`/projects/${projectId}`);
                
                // Update components dropdown
                const componentSelect = document.querySelector('[name="component_id"]');
                if (componentSelect && project.components) {
                    componentSelect.innerHTML = '<option value="">Select Component...</option>' +
                        project.components.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                }

                // Update versions dropdown
                const versionSelect = document.querySelector('[name="version_id"]');
                if (versionSelect && project.versions) {
                    versionSelect.innerHTML = '<option value="">Select Version...</option>' +
                        project.versions.map(v => `<option value="${v.id}">${v.name}</option>`).join('');
                }
            } catch (error) {
                console.error('Failed to load project options:', error);
            }
        },

        setupAutocomplete(input, type) {
            let timeout;
            const resultsDiv = document.createElement('div');
            resultsDiv.className = 'autocomplete-results d-none';
            input.parentNode.style.position = 'relative';
            input.parentNode.appendChild(resultsDiv);

            input.addEventListener('input', (e) => {
                clearTimeout(timeout);
                const query = e.target.value.trim();

                if (query.length < 2) {
                    resultsDiv.classList.add('d-none');
                    return;
                }

                timeout = setTimeout(async () => {
                    try {
                        const data = await api.get(`/${type}/search?q=${encodeURIComponent(query)}`);
                        this.showAutocompleteResults(resultsDiv, data.results, input);
                    } catch (error) {
                        console.error('Autocomplete error:', error);
                    }
                }, 300);
            });
        },

        showAutocompleteResults(container, results, input) {
            if (!results.length) {
                container.classList.add('d-none');
                return;
            }

            container.innerHTML = results.map(r => `
                <div class="autocomplete-item" data-id="${r.id}" data-name="${r.display_name || r.name}">
                    ${r.display_name || r.name}
                </div>
            `).join('');

            container.querySelectorAll('.autocomplete-item').forEach(item => {
                item.addEventListener('click', () => {
                    input.value = item.dataset.name;
                    const hiddenInput = document.querySelector(`[name="${input.name.replace('_search', '_id')}"]`);
                    if (hiddenInput) hiddenInput.value = item.dataset.id;
                    container.classList.add('d-none');
                });
            });

            container.classList.remove('d-none');
        },
    };

    // ==========================================
    // Confirm Delete
    // ==========================================
    const confirmDelete = {
        init() {
            document.querySelectorAll('[data-confirm]').forEach(el => {
                el.addEventListener('click', (e) => {
                    if (!confirm(el.dataset.confirm || 'Are you sure?')) {
                        e.preventDefault();
                    }
                });
            });
        },
    };

    // ==========================================
    // Auto-save Draft
    // ==========================================
    const autoSave = {
        init() {
            document.querySelectorAll('[data-autosave]').forEach(form => {
                const key = form.dataset.autosave;
                
                // Restore draft
                const draft = localStorage.getItem(key);
                if (draft) {
                    const data = JSON.parse(draft);
                    Object.keys(data).forEach(name => {
                        const input = form.querySelector(`[name="${name}"]`);
                        if (input) input.value = data[name];
                    });
                }

                // Save on change
                form.addEventListener('input', () => {
                    const data = {};
                    new FormData(form).forEach((value, key) => {
                        data[key] = value;
                    });
                    localStorage.setItem(key, JSON.stringify(data));
                });

                // Clear on submit
                form.addEventListener('submit', () => {
                    localStorage.removeItem(key);
                });
            });
        },
    };

    // ==========================================
    // Initialize Everything
    // ==========================================
    document.addEventListener('DOMContentLoaded', () => {
        toast.init();
        boardDragDrop.init();
        quickSearch.init();
        markdownEditor.init();
        issueForm.init();
        confirmDelete.init();
        autoSave.init();

        // Expose toast globally for external use
        window.toast = toast;
        window.api = api;
    });

})();
