<?php
// Configuração inicial
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

define('BASE_PATH', realpath(__DIR__ . '/../'));
require_once BASE_PATH . '/src/config/database.php';
require_once BASE_PATH . '/src/models/Produto.php';
require_once BASE_PATH . '/src/models/Usuario.php';

use App\Models\Produto;
use App\Models\Usuario;

$usuarioLogado = $_SESSION['usuario'] ?? null;
$isVendedor = $usuarioLogado && $usuarioLogado['tipo'] === 'vendedor';

try {
    $produtosSugeridos = Produto::listarSugestoes();
    $categorias = Produto::listarCategorias();
} catch (Exception $e) {
    $erro = "Erro ao carregar produtos: " . $e->getMessage();
}

$mensagem = $_SESSION['mensagem'] ?? null;
if ($mensagem) {
    unset($_SESSION['mensagem']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mac Cake - Confeitaria Artesanal Premium</title>
    <meta name="description" content="Confeitaria artesanal com os melhores bolos, doces e kits festa">
    
    <!-- Preload -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Dancing+Script:wght@700&display=swap" as="style">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style">
    
    <!-- Fontes -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" href="/assets/images/favicon.ico" type="image/x-icon">
</head>
<body class="bg-cream-50 font-sans antialiased">
    <!-- Header -->
    <?php include BASE_PATH . '/src/views/templates/header.php'; ?>

    <!-- Notificação -->
    <?php if ($mensagem): ?>
    <div class="flash-message fixed top-6 right-6 bg-green-500 text-white px-6 py-3 rounded-xl shadow-xl z-50 flex items-center gap-3 animate-fade-in">
        <?= htmlspecialchars($mensagem) ?>
        <button class="close-flash text-lg hover:scale-110 transition-transform">&times;</button>
    </div>
    <?php endif; ?>

    <main>
        <!-- Hero Banner com API do Unsplash -->
        <section class="hero-banner relative h-[85vh] min-h-[600px] flex items-center justify-center bg-gray-900/60 overflow-hidden">
            <img src="https://source.unsplash.com/random/1920x1080/?cake,bakery,sweet" 
                 alt="Confeitaria Mac Cake" 
                 class="absolute inset-0 w-full h-full object-cover blur-sm brightness-75 scale-105 animate-zoom-in-out">
            
            <div class="hero-content text-center px-6 z-10 max-w-5xl mx-auto animate-fade-up">
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold text-white mb-6 font-serif tracking-tight">
                    <span class="text-rose-300">Mac</span> Cake
                </h1>
                <p class="text-xl md:text-2xl text-rose-100 mb-10 max-w-2xl mx-auto leading-relaxed">
                    Transformamos momentos especiais em memórias doces com nossos produtos artesanais
                </p>
                <div class="hero-buttons flex flex-wrap justify-center gap-5">
                    <a href="produtos.php" class="btn-primary bg-rose-500 hover:bg-rose-600 text-white px-8 py-4 rounded-full font-medium transition-all transform hover:-translate-y-1 shadow-lg hover:shadow-xl flex items-center gap-2">
                        <i class="fas fa-birthday-cake"></i> Nossos Produtos
                    </a>
                    <?php if ($isVendedor): ?>
                        <a href="admin/produtos/adicionar.php" class="btn-secondary bg-white/90 hover:bg-white text-rose-600 px-6 py-4 rounded-full font-medium transition-all flex items-center gap-2 shadow-md">
                            <i class="fas fa-plus"></i> Adicionar Produto
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="absolute bottom-10 left-0 right-0 flex justify-center animate-bounce">
                <a href="#destaques" class="text-white text-2xl">
                    <i class="fas fa-chevron-down"></i>
                </a>
            </div>
        </section>

        <!-- Produtos em Destaque -->
        <section id="destaques" class="featured-products py-20 px-6 bg-gradient-to-b from-white to-cream-50">
            <div class="max-w-7xl mx-auto">
                <div class="section-header flex flex-col sm:flex-row justify-between items-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-star text-rose-500"></i> Nossos Destaques
                    </h2>
                    <a href="produtos.php" class="see-all text-rose-600 hover:text-rose-700 font-medium flex items-center gap-2 mt-4 sm:mt-0 group">
                        Ver todos <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                    </a>
                </div>
                
                <?php if (!empty($erro)): ?>
                    <div class="alert-error bg-red-50 text-red-700 p-4 rounded-lg mb-8 border-l-4 border-red-500 flex items-center gap-3 max-w-3xl mx-auto">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= $erro ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($produtosSugeridos)): ?>
                    <div class="empty-state text-center py-12 bg-white rounded-xl shadow-sm max-w-3xl mx-auto">
                        <i class="fas fa-box-open text-5xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">Nenhum produto em destaque no momento</p>
                        <a href="produtos.php" class="btn-primary inline-block mt-6 bg-rose-500 hover:bg-rose-600 text-white px-6 py-3 rounded-full">
                            Ver todos os produtos
                        </a>
                    </div>
                <?php else: ?>
                    <div class="products-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                        <?php foreach ($produtosSugeridos as $produto): ?>
                        <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-2 group">
                            <?php if ($produto['sugestao']): ?>
                                <div class="product-badge absolute top-4 right-4 bg-gradient-to-r from-rose-500 to-pink-500 text-white text-xs font-bold px-3 py-1 rounded-full z-10 shadow-md">
                                    <i class="fas fa-crown mr-1"></i> Destaque
                                </div>
                            <?php endif; ?>
                            
                            <a href="produto.php?id=<?= $produto['id'] ?>" class="product-link block">
                                <div class="product-image-container relative overflow-hidden h-64">
                                    <img src="<?= htmlspecialchars($produto['imagem']) ?>" 
                                        alt="<?= htmlspecialchars($produto['nome']) ?>"
                                        class="product-image w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                        loading="lazy">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                </div>
                                <div class="product-info p-6">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-rose-600 transition-colors"><?= htmlspecialchars($produto['nome']) ?></h3>
                                    <p class="product-description text-gray-600 text-sm mb-4 min-h-[60px]">
                                        <?= htmlspecialchars(mb_strimwidth($produto['descricao'], 0, 100, '...')) ?>
                                    </p>
                                    <div class="product-footer flex justify-between items-center">
                                        <span class="product-price text-rose-600 font-bold text-xl">
                                            R$ <?= number_format($produto['preco'], 2, ',', '.') ?>
                                        </span>
                                        <div class="product-actions">
                                            <button class="btn-cart bg-rose-100 hover:bg-rose-500 text-rose-600 hover:text-white w-12 h-12 rounded-full flex items-center justify-center transition-all shadow-md"
                                                    onclick="addToCart(event, <?= $produto['id'] ?>)">
                                                <i class="fas fa-shopping-cart"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Categorias -->
        <section class="categories py-20 px-6 bg-cream-100">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-900 mb-16 flex items-center justify-center gap-3">
                    <i class="fas fa-list text-rose-500"></i> Nossas Categorias
                </h2>
                <div class="categories-grid grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                    <?php foreach ($categorias as $categoria): ?>
                    <a href="produtos.php?categoria=<?= urlencode($categoria) ?>" 
                       class="category-card bg-white hover:bg-rose-50 rounded-2xl p-6 text-center transition-all duration-300 hover:shadow-lg hover:-translate-y-2 group">
                        <div class="category-icon bg-rose-100 group-hover:bg-gradient-to-br from-rose-400 to-pink-500 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 transition-all group-hover:scale-110 group-hover:text-white">
                            <?php if ($categoria === 'bolos'): ?>
                                <i class="fas fa-birthday-cake text-3xl text-rose-600 group-hover:text-white"></i>
                            <?php elseif ($categoria === 'doces'): ?>
                                <i class="fas fa-candy-cane text-3xl text-rose-600 group-hover:text-white"></i>
                            <?php else: ?>
                                <i class="fas fa-gift text-3xl text-rose-600 group-hover:text-white"></i>
                            <?php endif; ?>
                        </div>
                        <h3 class="font-medium text-gray-900 group-hover:text-rose-600 transition-colors"><?= ucfirst($categoria) ?></h3>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Depoimentos -->
        <section class="testimonials py-20 px-6 bg-white">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-900 mb-16 flex items-center justify-center gap-3">
                    <i class="fas fa-quote-left text-rose-500"></i> O que dizem sobre nós
                </h2>
                <div class="testimonials-slider grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="testimonial-card bg-cream-50 p-8 rounded-2xl shadow-sm relative overflow-hidden">
                        <div class="absolute -top-4 -right-4 text-rose-100 text-8xl z-0">
                            <i class="fas fa-quote-right"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="testimonial-content mb-6">
                                <p class="text-gray-700 italic text-lg relative z-10">"Os bolos da Mac Cake são incríveis! Sempre encomendo para meus eventos e todos elogiam. A qualidade é incomparável!"</p>
                            </div>
                            <div class="testimonial-author flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-rose-400 to-pink-500 flex items-center justify-center text-white">
                                    <span class="text-xl font-bold">A</span>
                                </div>
                                <div>
                                    <strong class="block font-semibold text-gray-900">Ana Silva</strong>
                                    <span class="text-sm text-gray-500">Cliente há 2 anos</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card bg-cream-50 p-8 rounded-2xl shadow-sm relative overflow-hidden">
                        <div class="absolute -top-4 -right-4 text-rose-100 text-8xl z-0">
                            <i class="fas fa-quote-right"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="testimonial-content mb-6">
                                <p class="text-gray-700 italic text-lg relative z-10">"Adoro a qualidade dos produtos e o atendimento personalizado. Fazem tudo com muito cuidado e carinho! Recomendo a todos."</p>
                            </div>
                            <div class="testimonial-author flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-rose-400 to-pink-500 flex items-center justify-center text-white">
                                    <span class="text-xl font-bold">C</span>
                                </div>
                                <div>
                                    <strong class="block font-semibold text-gray-900">Carlos Oliveira</strong>
                                    <span class="text-sm text-gray-500">Cliente há 1 ano</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Newsletter Moderna -->
        <section class="newsletter py-20 px-6 bg-gradient-to-r from-rose-500 to-pink-500 text-white relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('https://source.unsplash.com/random/600x400/?texture,pattern')] bg-cover opacity-10"></div>
            <div class="max-w-4xl mx-auto text-center relative z-10">
                <div class="bg-white/10 backdrop-blur-sm rounded-3xl p-10 border border-white/20">
                    <h2 class="text-3xl md:text-4xl font-bold mb-6 font-serif">Faça parte do nosso clube</h2>
                    <p class="text-xl mb-8 max-w-2xl mx-auto">Cadastre-se para receber ofertas exclusivas e novidades!</p>
                    <form class="newsletter-form flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                        <input type="email" placeholder="Seu melhor e-mail" required
                               class="flex-grow px-6 py-4 rounded-full bg-white/20 border border-white/30 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-rose-500">
                        <button type="submit" class="bg-white hover:bg-gray-100 text-rose-600 font-semibold px-8 py-4 rounded-full transition-all transform hover:-translate-y-1 shadow-lg hover:shadow-xl">
                            Cadastrar <i class="fas fa-paper-plane ml-2"></i>
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <?php include BASE_PATH . '/src/views/templates/footer.php'; ?>

    <script>
    // Função para adicionar ao carrinho
    async function addToCart(event, productId) {
        event.preventDefault();
        event.stopPropagation();
        
        <?php if ($usuarioLogado): ?>
            try {
                const button = event.currentTarget;
                const icon = button.querySelector('i');
                
                // Feedback visual
                button.disabled = true;
                button.classList.add('bg-green-500', 'text-white', 'scale-90');
                icon.classList.replace('fa-shopping-cart', 'fa-check');
                
                const response = await fetch('api/carrinho/adicionar.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        produto_id: productId,
                        quantidade: 1
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    await updateCartCount();
                    showAlert('✔️ Produto adicionado ao carrinho!', 'success');
                    
                    // Reset button after animation
                    setTimeout(() => {
                        button.classList.remove('bg-green-500', 'text-white', 'scale-90');
                        icon.classList.replace('fa-check', 'fa-shopping-cart');
                        button.disabled = false;
                    }, 2000);
                } else {
                    showAlert(data.message || 'Erro ao adicionar ao carrinho', 'error');
                    button.classList.remove('bg-green-500', 'text-white', 'scale-90');
                    icon.classList.replace('fa-check', 'fa-shopping-cart');
                    button.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Erro na comunicação com o servidor', 'error');
            }
        <?php else: ?>
            window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.pathname);
        <?php endif; ?>
    }

    // Atualiza contador do carrinho
    async function updateCartCount() {
        try {
            const response = await fetch('api/carrinho/contar.php');
            const data = await response.json();
            
            if (data.success) {
                let cartCount = document.getElementById('cart-count');
                const cartLink = document.querySelector('.carrinho-container a');
                
                if (!cartCount && data.count > 0) {
                    cartCount = document.createElement('span');
                    cartCount.id = 'cart-count';
                    cartCount.className = 'cart-count absolute -top-2 -right-2 bg-rose-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center animate-ping-once';
                    cartLink.appendChild(cartCount);
                }
                
                if (cartCount) {
                    cartCount.textContent = data.count;
                    cartCount.classList.toggle('hidden', data.count <= 0);
                    
                    if (data.count > 0) {
                        cartCount.classList.add('animate-ping-once');
                        setTimeout(() => {
                            cartCount.classList.remove('animate-ping-once');
                        }, 1000);
                    }
                }
            }
        } catch (error) {
            console.error('Error updating cart count:', error);
        }
    }

    // Mostra alerta flutuante
    function showAlert(message, type) {
        const alert = document.createElement('div');
        alert.className = `fixed top-6 right-6 px-6 py-4 rounded-xl shadow-xl z-50 flex items-center gap-3 animate-fade-in ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white`;
        alert.innerHTML = `
            <span>${message}</span>
            <button onclick="this.parentElement.remove()" class="text-lg hover:scale-110 transition-transform">&times;</button>
        `;
        document.body.appendChild(alert);
        setTimeout(() => {
            alert.classList.add('animate-fade-out');
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    }

    // Fecha mensagem flash
    document.querySelector('.close-flash')?.addEventListener('click', function() {
        this.parentElement.classList.add('animate-fade-out');
        setTimeout(() => this.parentElement.remove(), 300);
    });

    // Carrega contador do carrinho ao iniciar
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($usuarioLogado): ?>
            updateCartCount();
        <?php endif; ?>
        
        // Animações de scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-up');
                }
            });
        }, { threshold: 0.1 });
        
        document.querySelectorAll('.product-card, .category-card, .testimonial-card').forEach(card => {
            observer.observe(card);
        });
    });
    </script>
</body>
</html>