<script>
    class BaseClass {
        constructor(form, option = {}) {
        	this.no_set = [];
			this.number = [];
            if (option) {
                this.mode = option.mode || 'edit';
                this.scope = option.scope;
                this.sce = option.sce;
            }
            this.before(form);
			this.number.forEach(field => {
				Object.defineProperty(this, field, {
					get() {
						if (this[`_${field}_negative`]) return '-';
						if (this[`_${field}_dot`]) return this[`_${field}`] != null ? this[`_${field}`].toLocaleString('en') + this[`_${field}_dot`] : '';
						return this[`_${field}`] != null ? this[`_${field}`].toLocaleString('en') : '';
					},
					set(value) {
						let match_dot = (value || '').match(/\.0*$/);
						this[`_${field}_dot`] = match_dot ? match_dot[0] : null;
						this[`_${field}_negative`] = /^-$/.test(value);
						this[`_${field}`] = parseNumberString(value);
					}
				})
			})
            if (form) {
                for (let key in form) {
                    if (!this.no_set.includes(key)) this[key] = form[key];
                }
            }
            this.after(form);
        }

        before(form) {

        }

        after(form) {

        }

        get created_time() {
            if (!this.created_at) return moment().format('DD/MM/YYYY');
            return moment(this.created_at, 'YYYY-MM-DD').format('DD/MM/YYYY');
        }

        get creator() {
            return this.user_create ? this.user_create.name : DEFAULT_USER.fullname;
        }
    }
</script>
