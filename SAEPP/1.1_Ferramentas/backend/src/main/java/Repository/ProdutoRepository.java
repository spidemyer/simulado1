package com.saep.backend.Repository;

import java.util.List;

import org.springframework.data.jpa.repository.JpaRepository;

import com.saep.backend.Model.Produto;

public interface ProdutoRepository extends JpaRepository<Produto,Long>{

   //método para buscar por partes do nome[RF]
   List<Produto> findByNomeContainingIgnoreCase(String nome);
}