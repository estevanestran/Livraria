import React, { useState } from 'react';
import { createRoot } from 'react-dom/client';
import { HashRouter, Routes, Route, NavLink, Link } from 'react-router';
import './style.css';

const api = 'index.php?acao=api_recomendacoes';
const csrf = document.getElementById('recomendacoes-root')?.dataset.csrf || '';
function Inicio() {
  const [preferencias, setPreferencias] = useState('');
  const [carregando, setCarregando] = useState(false);
  const [erro, setErro] = useState('');
  const [resposta, setResposta] = useState(null);
  const consultar = async (e) => {
    e.preventDefault();
    setErro(''); setResposta(null); setCarregando(true);
    try {
      const res = await fetch(api, {
        method: 'POST', credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ preferencias, csrf }),
      });
      const dados = await res.json();
      if (!res.ok) throw new Error(dados.erro || `Erro ${res.status}`);
      setResposta(dados);
    } catch (err) { setErro(err.message || 'Erro de comunicação.'); }
    finally { setCarregando(false); }
  };
  return <section>
    <p>Descreva o tipo de livro que deseja ler. A inteligência artificial selecionará sugestões apenas entre os títulos disponíveis na livraria.</p>
    <form onSubmit={consultar} className="ia-form">
      <label htmlFor="preferencias" className="form-label">Quais histórias você gosta?</label>
      <textarea id="preferencias" className="form-control" required minLength={10} maxLength={500} rows={4}
        placeholder="Ex.: Gosto de fantasia com personagens complexos e um pouco de romance."
        value={preferencias} onChange={e => setPreferencias(e.target.value)} />
      <p className="small text-secondary">{preferencias.length}/500 caracteres</p>
      <button type="submit" className="btn btn-primary" disabled={carregando || preferencias.trim().length < 10}>
        {carregando ? 'Consultando a IA...' : 'Receber recomendações'}
      </button>
    </form>
    {erro && <div className="alert alert-danger mt-3" role="alert">{erro}</div>}
    {resposta && <div aria-live="polite" className="mt-4">
      <h2 className="h4">Suas recomendações</h2>
      {resposta.introducao && <p>{resposta.introducao}</p>}
      {!resposta.livros?.length && <p>Não encontramos títulos adequados no catálogo atual. Tente ampliar suas preferências.</p>}
      <div className="row row-cols-1 row-cols-md-2 g-3">
      {(resposta.livros || []).map(livro => <div className="col" key={livro.id}><article className="card h-100"><div className="card-body">
        <h3 className="h5">{livro.titulo}</h3><p className="text-secondary">{livro.autor}</p>
        <p>{livro.motivo}</p><p className="small">{livro.generos}</p>
        <p className="fw-bold">{Number(livro.preco).toLocaleString('pt-BR', {style:'currency',currency:'BRL'})}</p>
        <a href={`index.php?acao=livro&id=${livro.id}`} className="btn btn-outline-primary">Ver livro</a>
      </div></article></div>)}
      </div>
    </div>}
  </section>;
}
function Sobre() { return <section><h2 className="h4">Como funciona?</h2>
  <p>O formulário envia suas preferências, por AJAX, a uma rota PHP. O servidor consulta o banco MySQL e compartilha com a IA apenas os dados públicos dos livros disponíveis.</p>
  <p>A IA seleciona títulos do catálogo e explica as escolhas. As sugestões podem conter imprecisões; confirme os detalhes na página do livro.</p>
  <p>Não insira informações pessoais ou confidenciais no campo de preferências.</p>
  <Link to="/" className="btn btn-outline-primary">Experimentar recomendações</Link>
</section> }
function App() {
   return ( 
   <HashRouter>
    <nav className="ia-menu" aria-label="Navegação React">
      <NavLink to="/" end>Recomendações</NavLink>
      <NavLink to="/sobre">Como funciona</NavLink>
    </nav>
    <Routes>
      <Route path="/" element={<Inicio />}/>
      <Route path="/sobre" element={<Sobre />}/>
      <Route path="*" element={<p>Página não encontrada. <Link to="/">Voltar</Link></p>}/>
    </Routes>
    </HashRouter>
    );
  }
const root = document.getElementById('recomendacoes-root');
if (root) createRoot(root).render(<App />);
