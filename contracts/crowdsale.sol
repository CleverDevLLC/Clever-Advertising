pragma solidity ^0.4.12;
import './smarttokencontroller.sol';
import './utils.sol';
import './lib/if/token/ifsmarttoken.sol';

contract Crowdsale is SmartTokenController {
    uint256 public constant DURATION = 30 days;
    uint256 public constant TOKEN_PRICE_N = 1;
    uint256 public constant TOKEN_PRICE_D = 100;
    uint256 public constant BTCS_ETHER_CAP = 2000 ether;
    uint256 public constant MAX_GAS_PRICE = 2000000000 wei;

    string public version = '0.1';

    uint256 public startTime = 0;
    uint256 public endTime = 0;
    uint256 public totalEtherCap = 200000 ether;
    uint256 public totalEtherContributed = 0;
    bytes32 public realEtherCapHash;
    address public beneficiary = 0x10728c842B56A818e405b349CB60496036E18427;
    address public btcs = 1CP11drJREKDV8UUfH3FUtU6csrJ7xBLKX;

    event Contribution(address indexed _contributor, uint256 _amount, uint256 _return);

    function Crowdsale(IFSmartToken _token, uint256 _startTime, address _beneficiary, address _btcs, bytes32 _realEtherCapHash)
        SmartTokenController(_token)
        validAddress(_beneficiary)
        validAddress(btcs)
        earlierThan(_startTime)
        greaterThanZero(uint256(_realEtherCapHash))
    {
        startTime = _startTime;
        endTime = startTime + DURATION;
        beneficiary = _beneficiary;
        btcs = _btcs;
        realEtherCapHash = _realEtherCapHash;
    }

    modifier validGasPrice() {
        assert(tx.gasprice <= MAX_GAS_PRICE);
        _;
    }

    modifier validEtherCap(uint256 _cap, uint256 _key) {
        require(computeRealCap(_cap, _key) == realEtherCapHash);
        _;
    }

    modifier earlierThan(uint256 _time) {
        assert(now < _time);
        _;
    }

    modifier between(uint256 _startTime, uint256 _endTime) {
        assert(now >= _startTime && now < _endTime);
        _;
    }

    modifier btcsOnly() {
        assert(msg.sender == btcs);
        _;
    }

    modifier etherCapNotReached(uint256 _contribution) {
        assert(safeAdd(totalEtherContributed, _contribution) <= totalEtherCap);
        _;
    }

    modifier btcsEtherCapNotReached(uint256 _ethContribution) {
        assert(safeAdd(totalEtherContributed, _ethContribution) <= BTCS_ETHER_CAP);
        _;
    }

    function computeRealCap(uint256 _cap, uint256 _key) public constant returns (bytes32) {
        return keccak256(_cap, _key);
    }

    function enableRealCap(uint256 _cap, uint256 _key)
        public
        ownerOnly
        active
        between(startTime, endTime)
        validEtherCap(_cap, _key)
    {
        require(_cap < totalEtherCap);
        totalEtherCap = _cap;
    }

    function computeReturn(uint256 _contribution) public constant returns (uint256) {
        return safeMul(_contribution, TOKEN_PRICE_D) / TOKEN_PRICE_N;
    }

    function contributeETH()
        public
        payable
        between(startTime, endTime)
        returns (uint256 amount)
    {
        return processContribution();
    }

    function contributeBTCs()
        public
        payable
        btcsOnly
        btcsEtherCapNotReached(msg.value)
        earlierThan(startTime)
        returns (uint256 amount)
    {
        return processContribution();
    }

    function processContribution() private
        active
        etherCapNotReached(msg.value)
        validGasPrice
        returns (uint256 amount)
    {
        uint256 tokenAmount = computeReturn(msg.value);
        beneficiary.transfer(msg.value);
        totalEtherContributed = safeAdd(totalEtherContributed, msg.value);
        token.issue(msg.sender, tokenAmount);
        token.issue(beneficiary, tokenAmount);

        Contribution(msg.sender, msg.value, tokenAmount);
        return tokenAmount;
    }

    function() payable {
        contributeETH();
    }
}