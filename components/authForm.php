<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-4">
            <div class="card login-card">
                <div class="card-body">

                    <div class="text-center mb-4">
                        <span>Be Classical</span>
                        <hr>
                    </div>

                    <form action="controllers/auth.php" method="POST">

                        <div class="text-center my-3">

                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input 
                                        type="email" 
                                        class="form-control" 
                                        placeholder="Email" 
                                        name="email" 
                                        required
                                    >
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input 
                                        type="password" 
                                        class="form-control" 
                                        placeholder="Password" 
                                        name="password" 
                                        required
                                    >
                                    <span class="input-group-text">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button 
                                    type="submit" 
                                    class="btn btn-primary btn-get-started"
                                >
                                    GET STARTED
                                </button>
                            </div>

                        </div>

                    </form>

                    <div class="text-center mt-3">
                        <p>
                            Don't have an account? 
                            <a href="register.php" class="register-link text-white">
                                Register
                            </a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>