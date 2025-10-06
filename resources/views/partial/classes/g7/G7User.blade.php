<script>
    class G7User extends BaseClass {
        g7_employees = @json(App\Model\G7\G7Employee::getForSelect());
        no_set = [];

        before(form) {
            this.status = 1;
			this.type = 5;
			this.all_roles = @json(\App\Model\Common\Role::getForSelect());
			this.available_roles = this.all_roles.filter(val => val.type == this.type);
        }

        after(form) {
            if (!this.id) {
                this.password = "";
                this.password_confirm = "";
            }
        }

        get submit_data() {
            return {
                name: this.name,
                email: this.email,
                password: this.password,
                password_confirm: this.password_confirm,
                employee_id: this.employee_id,
                status: this.status,
				roles: this.roles,
            }
        }
    }
</script>
