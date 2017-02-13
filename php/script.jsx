var possibleCombinationSum = function(arr, n) {
  if (arr.indexOf(n) >= 0) { return true; }
  if (arr[0] > n) { return false; }
  if (arr[arr.length - 1] > n) {
    arr.pop();
    return possibleCombinationSum(arr, n);
  }
  var listSize = arr.length, combinationsCount = (1 << listSize)
  for (var i = 1; i < combinationsCount ; i++ ) {
    var combinationSum = 0;
    for (var j=0 ; j < listSize ; j++) {
      if (i & (1 << j)) { combinationSum += arr[j]; }
    }
    if (n === combinationSum) { return true; }
  }
  return false;
};

var StarFrame = React.createClass({
  render: function() {
    var numberOfStars = this.props.numberOfStars;
    
    var stars = [];
    for (var i = 0; i<numberOfStars; i++) {
      stars.push(
        <span className="glyphicon glyphicon-star"></span>
      );
    }
    
    return (
      <div id="stars-frame">
        <div className="well">
          {stars}
        </div>
      
      </div>
    );
  }
})

var ButtonFrame = React.createClass({
  render: function() {
    var disabled, button, correct = this.props.correct;
    
    switch(correct) {
      case true:
        button = (
          <button className="btn btn-success btn-lg"
                  onClick={this.props.acceptAnswer} >
            <span className="glyphicon glyphicon-ok"></span>
          </button>
        );
        break;
      case false:
        button = (
          <button className="btn btn-danger btn-lg">
            <span className="glyphicon glyphicon-remove"></span>
          </button>
        );
        break;
      default:
        disabled = (this.props.selectedNumbers.length === 0);
        button = (
          <button className="btn btn-primary btn-lg" disabled={disabled}
                  onClick={this.props.checkAnswer}>
           =
          </button>
        );
    }
    

    return (
      <div id="button-frame">
        {button}
        <br /><br />
        <button className="btn btn-warning btn-xs" onClick={this.props.redraw}
                disabled={this.props.redraws ===0} >
          <span className="glyphicon glyphicon-refresh"></span>
          &nbsp;
          {this.props.redraws}
        </button>
      </div>
    );
  }
})

var AnswerFrame = React.createClass({
  render: function() {
    var props=this.props;
    var selectedNumbers = props.selectedNumbers.map(function(i) {
      return (
        <span onClick={props.unselectNumber.bind(null, i)}>
          {i}
        </span>
      )
    });
    
    return (
      <div id="answer-frame">
        <div className="well">
          {selectedNumbers}
        </div>
  
      </div>
    );
  }
})

var NumberFrame = React.createClass({
  render: function() {
    
    var numbers = [], className, 
        selectNumber = this.props.selectNumber,
        usedNumbers = this.props.usedNumbers,
        selectedNumbers = this.props.selectedNumbers;
        
    for (var i=1; i <= 9; i++) {
      className = "number selected-" + (selectedNumbers.indexOf(i)>=0);
      className += " used-" + (usedNumbers.indexOf(i)>=0);
      numbers.push(
          <div className={className} onClick={selectNumber.bind(null, i)} >
            {i}
          </div>
      );
    }
    
    return (
      <div id="number-frame">
        <div className="well">
          {numbers}
        </div>
  
      </div>
    );
  }
})

var DoneFrame = React.createClass({
  render: function() {
    return (
    
      <div className="well text-center">
        <h2>{this.props.doneStatus}</h2>
        <button className="btn btn-default" 
                onClick={this.props.resetGame}>Play again</button>
      </div>
  

    );
  }
})

var Game = React.createClass({
  getInitialState: function() {
    return { numberOfStars: this.randomNumber(),
             selectedNumbers: [],
             usedNumbers: [],
             redraws: 5,
             correct: null,
             doneStatus: null };
  },
  resetGame: function() {
    this.replaceState(this.getInitialState());
  },
  randomNumber: function() {
    return Math.floor(Math.random()*9) + 1;
  },
  selectNumber: function(clickedNumber) {
    if (this.state.selectedNumbers.indexOf(clickedNumber) < 0) {
      this.setState(
        { selectedNumbers: this.state.selectedNumbers.concat(clickedNumber),
          correct: null }
      );
    }

  },
  unselectNumber: function(clickedNumber) {
    var selectedNumbers = this.state.selectedNumbers,
        indexOfNumber = selectedNumbers.indexOf(clickedNumber);
    
    selectedNumbers.splice(indexOfNumber, 1);
    
    this.setState({ selectedNumbers: selectedNumbers, correct: null });

  },
  sumOfSelectedNumbers: function() {
    return this.state.selectedNumbers.reduce(function(p,n) {
      return p+n;
    }, 0)
  },
  checkAnswer: function() {
    var correct = (this.state.numberOfStars === this.sumOfSelectedNumbers());
    this.setState({ correct: correct });
  },
  acceptAnswer: function() {
    var usedNumbers = this.state.usedNumbers.concat(this.state.selectedNumbers);
    this.setState({
      selectedNumbers: [],
      usedNumbers: usedNumbers,
      correct: null,
      numberOfStars: this.randomNumber()
    }, function() {
      this.updateDoneStatus();
    });
  },
  redraw: function() {
    if (this.state.redraws > 0){
      this.setState({
        numberOfStars: this.randomNumber(),
        selectedNumbers: [],
        correct: null,
        redraws: this.state.redraws -1
      }, function() {
        this.updateDoneStatus();
      });
    }
  },
  possibleSolution: function() {
    var numberOfStars = this.state.numberOfStars,
        possibleNumbers = [],
        usedNumbers = this.state.usedNumbers;
    
    for (var i=1; i<=9; i++) {
      if (usedNumbers.indexOf(i) <0) {
        possibleNumbers.push(i);
      }
    }
    
    return possibleCombinationSum(possibleNumbers, numberOfStars);
  },
  updateDoneStatus: function() {
    if (this.state.usedNumbers.length ===9) {
      this.setState({ doneStatus: 'Done. Nice!' });
      return;
    }
    if (this.state.redraws === 0 && !this.possibleSolution()) {
      this.setState({ doneStatus: 'Game Over!' })
    }
  },
  render: function() {
    var selectedNumbers = this.state.selectedNumbers,
        usedNumbers = this.state.usedNumbers,
        numberOfStars = this.state.numberOfStars,
        redraws = this.state.redraws,
        correct = this.state.correct,
        doneStatus = this.state.doneStatus,
        bottomFrame;
        
    if (doneStatus) {
      bottomFrame =  <DoneFrame doneStatus={doneStatus}
                                resetGame={this.resetGame} />;
    } else {
      bottomFrame = <NumberFrame selectedNumbers={selectedNumbers} 
                     usedNumbers={usedNumbers}
                     selectNumber={this.selectNumber} />;
    }
    
    return (
      <div id="game">
        <h2>Play Nine</h2>
        <hr />
        <div className="clearfix">
          <StarFrame numberOfStars={numberOfStars} />
          <ButtonFrame selectedNumbers={selectedNumbers}
                       correct={correct}
                       redraws={redraws}
                       checkAnswer={this.checkAnswer}
                       acceptAnswer={this.acceptAnswer} 
                       redraw={this.redraw} />
          <AnswerFrame selectedNumbers={selectedNumbers} 
                       unselectNumber={this.unselectNumber} />
        </div>
        
        {bottomFrame}
        
       
      </div>
    );
  }
})

React.render(<Game />, document.getElementById("root"));

/*exercise 1
var Button = React.createClass({
  localHandleClick: function() {
      this.props.localHandleClick(this.props.increment);
  },
  render: function() {
    return {counter: 0};
    
  {this.localHandleClick}>+{this.props.increment}</button>
    )
  }
});

var Result = React.createClass({
  render: function() {
    return (
      <div>{this.props.localCounter}</div>
    )
  }
});

var Main = React.createClass({
  getInitialState: function() {
    return {counter: 0};
    
  },
  handleClick: function(increment) {
    this.setState({ counter: this.state.counter+increment });
  },
  render: function() {
    return (
      <div>
        <Button localHandleClick={this.handleClick} increment={1} />
        <Button localHandleClick={this.handleClick} increment={5} />
        <Button localHandleClick={this.handleClick} increment={10} />
        <Button localHandleClick={this.handleClick} increment={100} />
        <Result localCounter={this.state.counter} />
      </div>
    )
  }
});
*/

/* exercise 2
var Card = React.createClass({
  getInitialState: function() {
    return {};
  },
  componentDidMount: function() {
    var component = this;
    $.get("https://api.github.com/users/"+ this.props.login, function(data) {
      component.setState(data);
    });
  },
  render: function() {
    return (
      <div>
          <img src={this.state.avatar_url} width="80" />
          <h3>{this.state.name}</h3>
          <hr/>
      </div>
    )
  }
});

var Form = React.createClass({
  handleSubmit: function(e) {
    e.preventDefault();
    var loginInput= React.findDOMNode(this.refs.login);
    this.props.addCard(loginInput.value);
    loginInput='';
  },
  render: function() {
    return (
      <form onSubmit={this.handleSubmit}>
        <input placeholder="github login" ref="login" />
        <button>Add</button>
      </form>
    )
  }
});

var Main = React.createClass({
  getInitialState: function() {
    return { logins: []};
  },
  addCard: function(loginToAdd) {
    this.setState({logins: this.state.logins.concat(loginToAdd)});
    
  },
  render: function() {
    var cards = this.state.logins.map(function(login){
      return (<Card login={login} />);
    });
    return (
       <div>
          <Form addCard={this.addCard} />
          {cards}
          
      </div>
    )
  }
});
*/