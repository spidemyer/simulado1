package com.saep.backend.Controller;

import java.util.List;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;
import com.saep.backend.Model.Movimentacao;
import com.saep.backend.Repository.MovimentacaoRepository;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RequestBody;

@RestController
@RequestMapping("/api/movimentacao")
public class MovimentacaoController {
    
    private final MovimentacaoRepository movimentacaoRepository;

    MovimentacaoController(MovimentacaoRepository movimentacaoRepository) {
        this.movimentacaoRepository = movimentacaoRepository;
    }

    //Métodos para buscar todas as movimentações
    @GetMapping()
    public List<Movimentacao> buscarMovimetacao() {
        return movimentacaoRepository.findAll();
    }

    @PostMapping()
    public Movimentacao registrarMovimentacao(@RequestBody Movimentacao movimentacao) {        
        return movimentacaoRepository.save(movimentacao);
    }
}