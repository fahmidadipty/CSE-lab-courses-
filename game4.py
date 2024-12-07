import chess
import random

# Simple evaluation function based on material value
def evaluate(board):
    # Material values for each piece type (simplified evaluation)
    piece_values = {
        chess.PAWN: 1,
        chess.KNIGHT: 3,
        chess.BISHOP: 3,
        chess.ROOK: 5,
        chess.QUEEN: 9,
        chess.KING: 0,  # The king is invaluable, so it's not considered in evaluation
    }

    evaluation = 0
    for piece_type in piece_values:
        evaluation += len(board.pieces(piece_type, chess.WHITE)) * piece_values[piece_type]
        evaluation -= len(board.pieces(piece_type, chess.BLACK)) * piece_values[piece_type]

    return evaluation

# Minimax algorithm with Alpha-Beta Pruning
def minimax(board, depth, alpha, beta, maximizing_player):
    # Base case: If we've reached the desired depth or the game is over
    if depth == 0 or board.is_game_over():
        return evaluate(board), None

    # Get all possible legal moves
    legal_moves = list(board.legal_moves)
    best_move = random.choice(legal_moves)  # Default to a random move if no better move is found

    if maximizing_player:
        # Maximizing for the AI (Maximizing player tries to get a higher score)
        max_eval = float('-inf')
        for move in legal_moves:
            board.push(move)
            evaluation, _ = minimax(board, depth - 1, alpha, beta, False)  # Minimize for opponent
            board.pop()
            if evaluation > max_eval:
                max_eval = evaluation
                best_move = move
            alpha = max(alpha, evaluation)
            if beta <= alpha:
                break  # Beta cut-off (pruning)
        return max_eval, best_move
    else:
        # Minimizing for the human player (Minimizing player tries to lower the score)
        min_eval = float('inf')
        for move in legal_moves:
            board.push(move)
            evaluation, _ = minimax(board, depth - 1, alpha, beta, True)  # Maximize for AI
            board.pop()
            if evaluation < min_eval:
                min_eval = evaluation
                best_move = move
            beta = min(beta, evaluation)
            if beta <= alpha:
                break  # Alpha cut-off (pruning)
        return min_eval, best_move

# Main game loop
def play_game():
    board = chess.Board()
    player_color = chess.WHITE  # You are playing as White (Player)
    ai_color = chess.BLACK  # AI is playing as Black

    print("Welcome to Chess! You are playing as White, and the AI plays as Black.")
    
    while True:
        print(board)
        
        # Player's turn (White)
        if board.turn == player_color:
            print("Your move:")
            move = input("Enter your move in UCI format (e.g., e2e4): ")
            if move == 'quit':
                break
            try:
                board.push_uci(move)
            except ValueError:
                print("Invalid move. Try again.")
                continue
        
        # AI's turn (Black)
        else:
            print("AI is thinking...")
            _, ai_move = minimax(board, 3, float('-inf'), float('inf'), True)  # Depth 3 for AI
            print(f"AI plays: {ai_move}")
            board.push(ai_move)

        if board.is_game_over():
            print("Game Over!")
            result = board.result()
            print(f"Result: {result}")
            break

# Run the game
if __name__ == "__main__":
    play_game()
