<script>
    class BaseChildClass {
        constructor(form, parent) {
        	this.no_set = [];
			this.number = [];
            this.parent = parent;
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
    }
</script>
