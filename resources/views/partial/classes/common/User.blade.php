<script>
    class User extends BaseClass {
        no_set = [];

        before(form) {
			this.all_roles = @json(\App\Model\Common\Role::getForSelect());
            this.image = {};
        }

        after(form) {
            if (!this.id) {
                this.password = "123456@";
                this.password_confirm = "123456@";
            }
        }

        get image() {
            return this._image;
        }

        set image(value) {
            this._image = new Image(value, this);
        }

        get submit_data() {
            let data = {
                name: this.name,
                email: this.email,
                account_name: this.account_name,
                phone_number: this.phone_number,
                password: this.password,
                password_confirm: this.password_confirm,
                roles: this.roles,
                status: this.status
            }

            data = jsonToFormData(data);
            let image = this.image.submit_data;
            if (image) data.append('image', image);
            return data;
        }
    }
</script>
